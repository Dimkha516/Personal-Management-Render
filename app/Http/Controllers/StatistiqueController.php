<?php

namespace App\Http\Controllers;

use App\Repositories\Statistics\CessationStatsProvider;
use App\Repositories\Statistics\CongeStatsProvider;
use App\Repositories\Statistics\EmployeStatsProvider;
use App\Services\StatistiqueService;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    protected $statsService;

    public function __construct(StatistiqueService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index()
    {
        return response()->json([
            'message' => 'Statistiques globales',
            'data' => $this->statsService->getAllStats(),
        ]);
    }

    public function show($entity)
    {
        return response()->json([
            'data' => $this->statsService->getStatsFor($entity),
        ]);
    }
}
