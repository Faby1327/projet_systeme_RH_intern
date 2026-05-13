<?php

namespace App\Models;

use CodeIgniter\Model;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;

class CongeModel extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'traite_par',
        'created_at',
    ];

    public function getByEmploye(int $employeId): array
    {
        return $this->db->table('v_conges_detail')
            ->where('employe_id', $employeId)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getRecentByEmploye(int $employeId, int $limit = 5): array
    {
        return $this->db->table('v_conges_detail')
            ->where('employe_id', $employeId)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getRecentAll(int $limit = 8, bool $employesSeulement = false): array
    {
        $builder = $this->db->table('v_conges_detail');

        if ($employesSeulement) {
            $builder->where('employe_id IN (SELECT id FROM employes WHERE role = "employe")', null, false);
        }

        return $builder
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getStatsForEmploye(int $employeId): array
    {
        $rows = $this->select('statut, COUNT(*) AS total')
            ->where('employe_id', $employeId)
            ->groupBy('statut')
            ->findAll();

        $stats = [
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
            'annulee' => 0,
        ];

        foreach ($rows as $row) {
            $statut = (string) $row['statut'];
            if (array_key_exists($statut, $stats)) {
                $stats[$statut] = (int) $row['total'];
            }
        }

        return $stats;
    }

    public function getStatsGlobal(): array
    {
        $rows = $this->select('statut, COUNT(*) AS total')
            ->groupBy('statut')
            ->findAll();

        $stats = [
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
            'annulee' => 0,
        ];

        foreach ($rows as $row) {
            $statut = (string) $row['statut'];
            if (array_key_exists($statut, $stats)) {
                $stats[$statut] = (int) $row['total'];
            }
        }

        return $stats;
    }

    public function countApprovedThisMonth(): int
    {
        return $this->where('statut', 'approuvee')
            ->where('strftime("%Y-%m", date_debut)', date('Y-m'))
            ->countAllResults();
    }

    public function getForRh(?int $departementId = null, ?string $statut = null): array
    {
        $builder = $this->db->table('v_conges_detail')
            ->where('employe_id IN (SELECT id FROM employes WHERE role = "employe")', null, false);

        if ($departementId !== null) {
            $builder->where('departement_id', $departementId);
        }

        if ($statut !== null && $statut !== '') {
            $builder->where('statut', $statut);
        }

        return $builder->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function hasOverlap(int $employeId, string $debut, string $fin, ?int $excludeId = null): bool
    {
        $builder = $this->where('employe_id', $employeId)
            ->whereIn('statut', ['en_attente', 'approuvee'])
            ->groupStart()
                ->where('date_debut <=', $fin)
                ->where('date_fin >=', $debut)
            ->groupEnd();

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function calculerJoursOuvrables(string $debut, string $fin): int
    {
        $dateDebut = new DateTimeImmutable($debut);
        $dateFin = new DateTimeImmutable($fin);

        if ($dateDebut > $dateFin) {
            return 0;
        }

        $period = new DatePeriod(
            $dateDebut,
            new DateInterval('P1D'),
            $dateFin->modify('+1 day')
        );

        $nbJours = 0;
        foreach ($period as $date) {
            $dayOfWeek = (int) $date->format('N');
            if ($dayOfWeek < 6) {
                $nbJours++;
            }
        }

        return $nbJours;
    }
}
