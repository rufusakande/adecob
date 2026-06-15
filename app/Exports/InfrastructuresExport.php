<?php

namespace App\Exports;

use App\Models\Infrastructure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InfrastructuresExport implements FromCollection, WithHeadings, WithTitle
{
    protected $infrastructures;
    protected $year;
    protected $filters;

    public function __construct($infrastructures, $year = null, $filters = [])
    {
        $this->infrastructures = $infrastructures;
        $this->year = $year;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->infrastructures->map(function ($infra) {
            return [
                'id' => $infra->id,
                'date' => $infra->date,
                'nom_enqueteur' => $infra->nom_enqueteur,
                'numero_telephone' => $infra->numero_telephone,
                'commune' => $infra->commune,
                'arrondissement' => is_array($infra->arrondissement) ? implode(', ', $infra->arrondissement) : $infra->arrondissement,
                'village' => $infra->village,
                'hameau' => $infra->hameau,
                'secteur_domaine' => $infra->secteur_domaine,
                'type_infrastructure' => $infra->type_infrastructure,
                'nom_infrastructure' => $infra->nom_infrastructure,
                'annee_realisation' => $infra->annee_realisation,
                'bailleur' => $infra->bailleur,
                'type_materiaux' => $infra->type_materiaux,
                'etat_fonctionnement' => $infra->etat_fonctionnement,
                'niveau_degradation' => $infra->niveau_degradation,
                'mode_gestion' => $infra->mode_gestion,
                'mode_gestion_preciser' => $infra->mode_gestion_preciser,
                'defectuosites_relevees' => $infra->defectuosites_relevees,
                'mesures_proposees' => $infra->mesures_proposees,
                'observation_generale' => $infra->observation_generale,
                'rehabilitation' => $infra->rehabilitation,
                'latitude' => $infra->latitude,
                'longitude' => $infra->longitude,
                'altitude' => $infra->altitude,
                'precision' => $infra->precision,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Nom Enquêteur',
            'Numéro Téléphone',
            'Commune',
            'Arrondissement',
            'Village',
            'Hameau',
            'Secteur Domaine',
            'Type Infrastructure',
            'Nom Infrastructure',
            'Année Réalisation',
            'Bailleur',
            'Type Matériaux',
            'État Fonctionnement',
            'Niveau Dégradation',
            'Mode Gestion',
            'Mode Gestion Préciser',
            'Défectuosités Relevées',
            'Mesures Proposées',
            'Observation Générale',
            'Réhabilitation',
            'Latitude',
            'Longitude',
            'Altitude',
            'Précision',
        ];
    }

    public function title(): string
    {
        $title = 'Infrastructures';
        if ($this->year) {
            $title .= '_' . $this->year;
        }
        if (!empty($this->filters['commune'])) {
            $title .= '_' . $this->filters['commune'];
        }
        return $title;
    }
}
