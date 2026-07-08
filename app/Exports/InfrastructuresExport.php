<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class InfrastructuresExport implements FromQuery, WithMapping, WithHeadings, WithTitle, WithChunkReading
{
    protected Builder $query;
    protected $year;
    protected $filters;

    public function __construct(Builder $query, $year = null, $filters = [])
    {
        $this->query = $query;
        $this->year = $year;
        $this->filters = $filters;
    }

    public function query()
    {
        return $this->query->select([
            'id',
            'date',
            'nom_enqueteur',
            'numero_telephone',
            'commune',
            'arrondissement',
            'village',
            'hameau',
            'secteur_domaine',
            'type_infrastructure',
            'nom_infrastructure',
            'annee_realisation',
            'bailleur',
            'type_materiaux',
            'etat_fonctionnement',
            'niveau_degradation',
            'mode_gestion',
            'mode_gestion_preciser',
            'defectuosites_relevees',
            'mesures_proposees',
            'observation_generale',
            'rehabilitation',
            'latitude',
            'longitude',
            'altitude',
            'precision',
        ])->orderBy('id');
    }

    public function map($infra): array
    {
        return [
            $infra->id,
            $infra->date,
            $infra->nom_enqueteur,
            $infra->numero_telephone,
            $infra->commune,
            is_array($infra->arrondissement) ? implode(', ', $infra->arrondissement) : $infra->arrondissement,
            $infra->village,
            $infra->hameau,
            $infra->secteur_domaine,
            $infra->type_infrastructure,
            $infra->nom_infrastructure,
            $infra->annee_realisation,
            $infra->bailleur,
            $infra->type_materiaux,
            $infra->etat_fonctionnement,
            $infra->niveau_degradation,
            $infra->mode_gestion,
            $infra->mode_gestion_preciser,
            $infra->defectuosites_relevees,
            $infra->mesures_proposees,
            $infra->observation_generale,
            $infra->rehabilitation,
            $infra->latitude,
            $infra->longitude,
            $infra->altitude,
            $infra->precision,
        ];
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

    public function chunkSize(): int
    {
        return 1000;
    }
}
