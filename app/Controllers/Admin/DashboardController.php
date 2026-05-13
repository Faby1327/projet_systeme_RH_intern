<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\EmployeModel;

class DashboardController extends BaseController
{
    private EmployeModel $employeModel;
    private CongeModel $congeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
        $this->congeModel = new CongeModel();
    }

    public function index(): string
    {
        $stats = $this->congeModel->getStatsGlobal();

        return view('admin/dashboard', [
            'employesActifs' => $this->employeModel
                ->where('role', 'employe')
                ->where('actif', 1)
                ->countAllResults(),
            'departementsCount' => db_connect()->table('departements')->countAllResults(),
            'approvedThisMonth' => $this->congeModel->countApprovedThisMonth(),
            'pendingDemandes' => $stats['en_attente'] ?? 0,
            'recentDemandes' => $this->congeModel->getRecentAll(),
        ]);
    }
}
