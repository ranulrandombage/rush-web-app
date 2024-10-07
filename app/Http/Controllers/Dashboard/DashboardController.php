<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Service\CostingService;
use App\Service\PartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private PartService $partService;
    private CostingService $costingService;

    public function __construct(CostingService $costingService, PartService $partService)
    {

        $this->costingService = $costingService;
        $this->partService = $partService;
    }

    /**
     * Load all costings
     * @param \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function _index(Request $request)
    {
        try {
            $costings = $this->costingService->getAllCostings();
            $latestCosting=$costings->first();
            $latestCostingParts=$latestCosting?$this->costingService->getCostingParts($latestCosting):null;
            $parts= $this->partService->getAllParts();
            $partsCount=$parts->count();
            $data = (object)[
                "latest_costing_id"=>$latestCosting?$latestCosting->id:null,
                "total_costing" => $costings?$costings->count():0,
                "latest_costing_recorded_at" => $latestCosting?$latestCosting->created_at->diffForHumans():null,
                "total_part"=>$partsCount,
                "latest_part_recorded_at"=>$partsCount!==0?$parts->first()->created_at->diffForHumans():null,
                "latest_net_selling_prices"=> $latestCostingParts?->net_selling_price,
                "latest_net_profit"=>$latestCostingParts?->net_profit
            ];
            return view('sections.dashboard.index', ["data" => $data, "success" => $request->success, "error" => $request->error]);
        } catch (\Exception $e) {
            return view('sections.dashboard.index', ["error" => $e->getMessage()]);
        }

    }
}
