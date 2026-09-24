<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * Export Excel des fiches de planification (triennal / annuel).
 *
 * Reproduit la présentation officielle du PDF : en-tête institutionnel avec les
 * deux logos (armoiries + logo de la commune), titre de la fiche, bloc de
 * renseignements (département, commune, dates, exercices) puis le tableau.
 *
 * Les montants sont écrits comme de vrais nombres (format « #,##0 ») afin qu'ils
 * restent exploitables dans Excel (sommes, tris) tout en s'affichant comme au PDF.
 */
class PlannedPlanExport implements FromArray, WithDrawings, WithEvents, WithTitle
{
    public const MODE_TRIENNAL = 'triennal';
    public const MODE_ANNUEL   = 'annual';

    /** Ligne des en-têtes de colonnes (le bloc d'en-tête occupe les lignes 1 à 10). */
    private const ROW_HEADING = 12;
    /** Première ligne de données. */
    private const ROW_FIRST_DATA = 13;

    private Collection $infrastructures;
    private string $mode;
    private array $meta;
    private ?string $armoiriesPath;
    private ?string $communeLogoPath;

    public function __construct(
        Collection $infrastructures,
        string $mode = self::MODE_TRIENNAL,
        array $meta = [],
        ?string $armoiriesPath = null,
        ?string $communeLogoPath = null
    ) {
        $this->infrastructures  = $infrastructures;
        $this->mode             = $mode === self::MODE_ANNUEL ? self::MODE_ANNUEL : self::MODE_TRIENNAL;
        $this->meta             = $meta;
        $this->armoiriesPath    = $armoiriesPath;
        $this->communeLogoPath  = $communeLogoPath;
    }

    // ---------------------------------------------------------------------
    // Structure du classeur
    // ---------------------------------------------------------------------

    public function title(): string
    {
        return $this->mode === self::MODE_ANNUEL ? 'Plan annuel' : 'Plan triennal';
    }

    /** Nombre de colonnes du tableau (A..K pour le triennal, A..L pour l'annuel). */
    private function columns(): array
    {
        return $this->mode === self::MODE_ANNUEL
            ? ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L']
            : ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
    }

    private function lastColumn(): string
    {
        $cols = $this->columns();
        return end($cols);
    }

    private function isAnnual(): bool
    {
        return $this->mode === self::MODE_ANNUEL;
    }

    /** Libellés des colonnes (1 ou 2 lignes : l'annuel regroupe les 4 trimestres). */
    private function headingsRow1(): array
    {
        if ($this->isAnnual()) {
            return [
                'ID',
                "Localisation de l'infrastructure\n(Commune, Arrondissement, Village/Quartier, Coordonnées GPS)",
                "Secteur / Type\nd'infrastructure",
                'Description des travaux à réaliser',
                'Budget annuel (FCFA)',
                'Trimestre (FCFA)', '', '', '',
                'Acteur(s) concerné(s)',
                "Statut\nd'exécution",
                'Observations / justificatifs',
            ];
        }

        return [
            'ID',
            "Localisation de l'infrastructure\n(Commune, Arrondissement, Village/Quartier, Coordonnées GPS)",
            "Secteur / Type\nd'infrastructure",
            'Description des travaux à réaliser',
            'Unité',
            'Quantité',
            'Coût unitaire (FCFA)',
            'Répartition (FCFA)',
            'Acteur(s) concerné(s)',
            'Source de financement',
            'Priorité',
        ];
    }

    private function headingsRow2(): array
    {
        return ['', '', '', '', '', 'T1', 'T2', 'T3', 'T4', '', '', ''];
    }

    public function array(): array
    {
        $cols = $this->columns();
        $empty = array_fill(0, count($cols), '');

        $rows = [];

        // 1 à 4 : en-tête institutionnel (les logos sont ajoutés via WithDrawings)
        $rows[] = $empty;                       // 1
        $rows[] = $empty;                       // 2
        $rows[] = $empty;                       // 3
        $rows[] = $empty;                       // 4

        // 5 : titre de la fiche
        $rows[] = array_merge([$this->titleLine()], array_fill(0, count($cols) - 1, ''));  // 5
        $rows[] = $empty;                       // 6

        // 7 à 9 : renseignements
        $rows[] = array_merge([$this->metaLine('Département :', $this->meta['departement'] ?? null)], array_fill(0, count($cols) - 1, ''));  // 7
        $rows[] = array_merge([$this->metaLine('Commune :', $this->meta['communeName'] ?? null)], array_fill(0, count($cols) - 1, ''));        // 8
        $rows[] = array_merge([$this->metaLine("Date d'élaboration :", $this->meta['dateElaboration'] ?? null)], array_fill(0, count($cols) - 1, '')); // 9
        $rows[] = array_merge([$this->exerciceLine()], array_fill(0, count($cols) - 1, ''));  // 10
        $rows[] = $empty;                       // 11

        // 12 (+13 pour l'annuel) : en-têtes du tableau
        $rows[] = $this->headingsRow1();
        if ($this->isAnnual()) {
            $rows[] = $this->headingsRow2();
        }

        // Données
        foreach ($this->infrastructures as $infra) {
            $plan = $infra->works->where('status', 'planned')->sortBy('completion_date')->first();
            if (!$plan) {
                continue;
            }
            $rows[] = $this->isAnnual()
                ? $this->annualRow($infra, $plan)
                : $this->triennialRow($infra, $plan);
        }

        return $rows;
    }

    // ---------------------------------------------------------------------
    // Contenu
    // ---------------------------------------------------------------------

    private function titleLine(): string
    {
        return $this->isAnnual()
            ? "FICHE DE PLANIFICATION ANNUELLE D'ENTRETIEN DES INFRASTRUCTURES COMMUNALES"
            : "FICHE DE PLANIFICATION TRIENNAL D'ENTRETIEN DES INFRASTRUCTURES COMMUNALES";
    }

    private function metaLine(string $label, ?string $value): string
    {
        return $label . ' ' . ($value ?: '…………………………');
    }

    private function exerciceLine(): string
    {
        if ($this->isAnnual()) {
            $annee = $this->meta['anneeExport'] ?? $this->meta['anneeBase'] ?? null;
            return 'Exercice budgétaire : ' . ($annee ?: '…………………………');
        }

        $debut = $this->meta['anneeDebut'] ?? $this->meta['anneeBase'] ?? null;
        $fin   = $this->meta['anneeFin'] ?? ($debut ? $debut + 2 : null);

        return 'Exercices budgétaires : ' . ($debut ?: '……') . ' à ' . ($fin ?: '……');
    }

    private function localisation($infra): string
    {
        $arr = is_array($infra->arrondissement)
            ? $infra->arrondissement
            : (json_decode((string) $infra->arrondissement, true) ?: []);
        $arrText = is_array($arr) && count($arr) ? implode(', ', $arr) : ((string) $infra->arrondissement);

        $gps = ($infra->latitude && $infra->longitude)
            ? '(' . number_format((float) $infra->latitude, 4, '.', '') . ' ; ' . number_format((float) $infra->longitude, 4, '.', '') . ')'
            : '';

        return trim(
            ($infra->commune ?: '')
            . ($arrText ? ' – Arr. ' . $arrText : '')
            . ($infra->village ? ' – ' . $infra->village : '')
            . ($infra->hameau ? ' / ' . $infra->hameau : '')
            . ($gps ? ' ' . $gps : ''),
            ' –'
        ) ?: '—';
    }

    private function secteurType($infra): string
    {
        return trim(($infra->secteur_domaine ?: '') . ($infra->type_infrastructure ? ' / ' . $infra->type_infrastructure : ''), ' /') ?: '—';
    }

    private function triennialRow($infra, $plan): array
    {
        $repartition = [];
        foreach ($plan->anneeRangeYears() as $yr) {
            $v = $plan->repartitionForYear($yr);
            $repartition[] = $yr . ' : ' . (($v !== null && $v !== '') ? number_format((float) $v, 0, ',', ' ') : '0');
        }

        return [
            $infra->id,
            $this->localisation($infra),
            $this->secteurType($infra),
            $plan->description ?: ($infra->mesures_proposees ?: '—'),
            $plan->unite ?: '—',
            $plan->quantite !== null ? (float) $plan->quantite : '—',
            $plan->cout_unitaire !== null ? (float) $plan->cout_unitaire : '—',
            $repartition ? implode("\n", $repartition) : '—',
            $plan->acteurs_concernes ?: ($plan->provider_name ?: '—'),
            $plan->sources_financement ?: '—',
            $plan->priorite ?: '—',
        ];
    }

    private function annualRow($infra, $plan): array
    {
        $anneeX = (int) ($this->meta['anneeExport'] ?? $this->meta['anneeBase'] ?? now()->year);
        $budget = $plan->repartitionForYear($anneeX);
        $tris   = $plan->trimestresForYear($anneeX) ?? [];

        return [
            $infra->id,
            $this->localisation($infra),
            $this->secteurType($infra),
            $plan->description ?: ($infra->mesures_proposees ?: '—'),
            $budget !== null ? (float) $budget : '—',
            $tris['t1'] ?? null,
            $tris['t2'] ?? null,
            $tris['t3'] ?? null,
            $tris['t4'] ?? null,
            $plan->acteurs_concernes ?: ($plan->provider_name ?: '—'),
            $plan->statut_execution ?: '—',
            $plan->observations ?: '—',
        ];
    }

    // ---------------------------------------------------------------------
    // Logos
    // ---------------------------------------------------------------------

    public function drawings()
    {
        $drawings = [];

        if ($this->armoiriesPath && is_file($this->armoiriesPath)) {
            $armoiries = new Drawing();
            $armoiries->setName('Armoiries');
            $armoiries->setPath($this->armoiriesPath);
            $armoiries->setResizeProportional(true);
            $armoiries->setHeight(58);
            $armoiries->setOffsetX(8);
            $armoiries->setOffsetY(4);
            $armoiries->setCoordinates('A1');
            $drawings[] = $armoiries;
        }

        if ($this->communeLogoPath && is_file($this->communeLogoPath)) {
            $logo = new Drawing();
            $logo->setName('Logo commune');
            $logo->setPath($this->communeLogoPath);
            $logo->setResizeProportional(true);
            $logo->setHeight(60);
            $logo->setOffsetX(6);
            $logo->setOffsetY(4);
            // Ancré sur la dernière colonne du bloc d'en-tête, aligné à droite du titre.
            $logo->setCoordinates($this->lastColumn() . '1');
            $drawings[] = $logo;
        }

        return $drawings;
    }

    // ---------------------------------------------------------------------
    // Mise en forme
    // ---------------------------------------------------------------------

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $cols  = $this->columns();
                $last  = $this->lastColumn();
                $width = count($cols);

                $headingRow = self::ROW_HEADING;
                $firstData  = self::ROW_FIRST_DATA;
                $lastRow    = max($sheet->getHighestRow(), $firstData - 1);

                // ---- Bandeau institutionnel ----------------------------------
                $sheet->mergeCells("B2:" . ($this->isAnnual() ? 'J2' : 'I2'));
                $sheet->setCellValue('B2', 'RÉPUBLIQUE DU BÉNIN');
                $sheet->mergeCells("B3:" . ($this->isAnnual() ? 'J3' : 'I3'));
                $sheet->setCellValue('B3', 'MINISTÈRE DE LA DÉCENTRALISATION ET DE LA GOUVERNANCE LOCALE (MDGL)');

                foreach (['B2', 'B3'] as $c) {
                    $sheet->getStyle($c)->getFont()->setBold(true)->setSize($c === 'B2' ? 12 : 10);
                    $sheet->getStyle($c)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                // ---- Titre ----------------------------------------------------
                $sheet->mergeCells("A5:{$last}5");
                $sheet->getStyle("A5:{$last}5")->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle("A5:{$last}5")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // ---- Renseignements ------------------------------------------
                $metaWidth = min(6, $width);
                foreach ([7, 8, 9, 10] as $r) {
                    $sheet->mergeCells("A{$r}:" . $cols[$metaWidth - 1] . $r);
                    $sheet->getStyle("A{$r}")->getFont()->setSize(10);
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
                // « Commune » et la date d'élaboration sur la même ligne visuelle que
                // dans le PDF : on aligne le second bloc à droite du premier.
                $sheet->getStyle('A7:A10')->getFont()->setBold(true);

                // ---- En-têtes du tableau -------------------------------------
                $headTop = $headingRow;
                $headBottom = $this->isAnnual() ? $headingRow + 1 : $headingRow;

                if ($this->isAnnual()) {
                    // Colonnes fusionnées verticalement (ID, localisation, ... observations)
                    $sheet->mergeCells("A{$headTop}:A{$headBottom}");
                    $sheet->mergeCells("B{$headTop}:B{$headBottom}");
                    $sheet->mergeCells("C{$headTop}:C{$headBottom}");
                    $sheet->mergeCells("D{$headTop}:D{$headBottom}");
                    $sheet->mergeCells("E{$headTop}:E{$headBottom}");
                    $sheet->mergeCells("F{$headTop}:I{$headTop}");   // « Trimestre (FCFA) »
                    $sheet->mergeCells("J{$headTop}:J{$headBottom}");
                    $sheet->mergeCells("K{$headTop}:K{$headBottom}");
                    $sheet->mergeCells("L{$headTop}:L{$headBottom}");
                }

                $headerRange = "A{$headTop}:{$last}{$headBottom}";
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9);
                $sheet->getStyle($headerRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD9E1D5');
                $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setARGB('FF666666');

                // ---- Corps du tableau ----------------------------------------
                if ($lastRow >= $firstData) {
                    $dataRange = "A{$firstData}:{$last}{$lastRow}";
                    $sheet->getStyle($dataRange)->getAlignment()
                        ->setVertical(Alignment::VERTICAL_TOP)
                        ->setWrapText(true);
                    $sheet->getStyle($dataRange)->getFont()->setSize(9);
                    $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->getColor()->setARGB('FF999999');

                    // Colonnes centrées (ID, unité, quantité, montants, priorité/statut)
                    $centered = $this->isAnnual()
                        ? ['A', 'E', 'F', 'G', 'H', 'I', 'K']
                        : ['A', 'E', 'F', 'G', 'K'];
                    foreach ($centered as $c) {
                        $sheet->getStyle("{$c}{$firstData}:{$c}{$lastRow}")
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Montants : format numérique avec séparateur de milliers
                    $money = $this->isAnnual()
                        ? ['E', 'F', 'G', 'H', 'I']
                        : ['G'];
                    foreach ($money as $c) {
                        $sheet->getStyle("{$c}{$firstData}:{$c}{$lastRow}")
                            ->getNumberFormat()->setFormatCode('#,##0');
                    }

                    // Lignes alternées pour la lisibilité
                    for ($r = $firstData; $r <= $lastRow; $r++) {
                        if (($r - $firstData) % 2 === 1) {
                            $sheet->getStyle("A{$r}:{$last}{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF7F9F7');
                        }
                        $sheet->getRowDimension($r)->setRowHeight(-1);
                    }
                }

                // ---- Dimensions -------------------------------------------------
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getRowDimension(4)->setRowHeight(10);
                $sheet->getRowDimension(5)->setRowHeight(22);
                $sheet->getRowDimension($headTop)->setRowHeight(30);
                if ($this->isAnnual()) {
                    $sheet->getRowDimension($headBottom)->setRowHeight(16);
                }

                $widths = $this->isAnnual()
                    ? ['A' => 6, 'B' => 42, 'C' => 22, 'D' => 40, 'E' => 16, 'F' => 12, 'G' => 12, 'H' => 12, 'I' => 12, 'J' => 26, 'K' => 16, 'L' => 26]
                    : ['A' => 6, 'B' => 42, 'C' => 22, 'D' => 40, 'E' => 11, 'F' => 10, 'G' => 16, 'H' => 24, 'I' => 26, 'J' => 24, 'K' => 12];

                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // ---- Mise en page (impression proche du PDF) ---------------------
                $page = $sheet->getPageSetup();
                $page->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $page->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $page->setFitToWidth(1);
                $page->setFitToHeight(0);
                $page->setRowsToRepeatAtTopByStartAndEnd($headTop, $headBottom);

                $sheet->getPageMargins()->setTop(0.4)->setRight(0.3)->setLeft(0.3)->setBottom(0.4);
            },
        ];
    }
}
