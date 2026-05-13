<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'annee',
        'jours_attribues',
        'jours_pris',
    ];

    public function ensureYearInitializedForEmploye(int $employeId, int $annee): void
    {
        $typeCongeModel = new TypeCongeModel();
        $types = $typeCongeModel->orderBy('id', 'ASC')->findAll();

        if ($types === []) {
            return;
        }

        $existingTypeIds = array_map(
            static fn (array $row): int => (int) $row['type_conge_id'],
            $this->select('type_conge_id')
                ->where('employe_id', $employeId)
                ->where('annee', $annee)
                ->findAll()
        );

        $rows = [];
        foreach ($types as $type) {
            if (in_array((int) $type['id'], $existingTypeIds, true)) {
                continue;
            }

            $rows[] = [
                'employe_id' => $employeId,
                'type_conge_id' => (int) $type['id'],
                'annee' => $annee,
                'jours_attribues' => (int) $type['jours_annuels'],
                'jours_pris' => 0,
            ];
        }

        if ($rows !== []) {
            $this->insertBatch($rows);
        }
    }

    public function getByEmployeForYear(int $employeId, int $annee): array
    {
        $this->ensureYearInitializedForEmploye($employeId, $annee);

        return $this->db->table('v_soldes_detail')
            ->where('employe_id', $employeId)
            ->where('annee', $annee)
            ->orderBy('type_conge_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getRestant(int $employeId, int $typeId, int $annee): int
    {
        $this->ensureYearInitializedForEmploye($employeId, $annee);

        $solde = $this->where([
            'employe_id' => $employeId,
            'type_conge_id' => $typeId,
            'annee' => $annee,
        ])->first();

        if ($solde === null) {
            return 0;
        }

        return (int) $solde['jours_attribues'] - (int) $solde['jours_pris'];
    }

    public function hasSufficientBalance(int $employeId, int $typeId, int $annee, int $nbJours): bool
    {
        return $this->getRestant($employeId, $typeId, $annee) >= $nbJours;
    }

    public function debiter(int $employeId, int $typeId, int $annee, int $nbJours): bool
    {
        $this->ensureYearInitializedForEmploye($employeId, $annee);

        return (bool) $this->where([
            'employe_id' => $employeId,
            'type_conge_id' => $typeId,
            'annee' => $annee,
        ])->set('jours_pris', 'jours_pris + ' . (int) $nbJours, false)->update();
    }

    public function crediter(int $employeId, int $typeId, int $annee, int $nbJours): bool
    {
        $this->ensureYearInitializedForEmploye($employeId, $annee);

        return (bool) $this->where([
            'employe_id' => $employeId,
            'type_conge_id' => $typeId,
            'annee' => $annee,
        ])->set('jours_pris', 'MAX(jours_pris - ' . (int) $nbJours . ', 0)', false)->update();
    }

    public function getAllForYear(int $annee): array
    {
        return $this->db->table('v_soldes_detail')
            ->where('annee', $annee)
            ->where('employe_id IN (SELECT id FROM employes WHERE role = "employe")', null, false)
            ->orderBy('employe_nom', 'ASC')
            ->orderBy('employe_prenom', 'ASC')
            ->orderBy('type_conge_id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
