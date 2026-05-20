<?php

namespace App\Controllers\Employe;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;

class DashboardController extends BaseController
{
    private SoldeModel $soldeModel;
    private CongeModel $congeModel;

    public function __construct()
    {
        $this->soldeModel = new SoldeModel();
        $this->congeModel = new CongeModel();
    }

    public function index(): string
    {
        $employeId = (int) session('user_id');
        $annee = (int) date('Y');
        $soldes = $this->soldeModel->getByEmployeForYear($employeId, $annee);
        $stats = $this->congeModel->getStatsForEmploye($employeId);

        $joursAttribues = 0;
        $joursPris = 0;
        foreach ($soldes as $solde) {
            $joursAttribues += (int) $solde['jours_attribues'];
            $joursPris += (int) $solde['jours_pris'];
        }

        return view('employe/dashboard', [
            'annee' => $annee,
            'soldes' => $soldes,
            'recentConges' => $this->congeModel->getRecentByEmploye($employeId),
            'dashboardConges' => $this->congeModel->getByEmploye($employeId),
            'congesByType' => $this->congeModel->getCountByTypeForEmploye($employeId),
            'stats' => $stats,
            'joursRestants' => $joursAttribues - $joursPris,
            'joursAttribues' => $joursAttribues,
        ]);
    }
}
