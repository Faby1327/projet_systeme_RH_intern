<?php

namespace App\Controllers\Rh;

use App\Controllers\BaseController;
use App\Models\CongeModel;

class DashboardController extends BaseController
{
    private CongeModel $congeModel;

    public function __construct()
    {
        $this->congeModel = new CongeModel();
    }

    public function index(): string
    {
        return view('rh/dashboard', [
            'stats' => $this->congeModel->getStatsGlobal(),
            'approvedThisMonth' => $this->congeModel->countApprovedThisMonth(),
            'recentDemandes' => $this->congeModel->getRecentAll(8, true),
        ]);
    }
}
