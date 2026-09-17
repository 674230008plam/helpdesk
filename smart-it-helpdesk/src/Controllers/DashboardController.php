<?php
namespace App\Controllers;

use App\Services\DashboardService;

class DashboardController
{
    public function index(): void
    {
        $service = new DashboardService();
        $stats = $service->getStatistics();
        require_once dirname(__DIR__, 2) . '/views/admin/dashboard.php';
    }
}
