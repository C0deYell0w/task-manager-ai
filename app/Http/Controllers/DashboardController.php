<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Inject the DashboardService dependency.
     */
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Render the main dashboard view with relevant task metrics.
     * 
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $metrics = $this->dashboardService->getDashboardMetrics($request->user());

        return Inertia::render('Dashboard', $metrics);
    }
}
