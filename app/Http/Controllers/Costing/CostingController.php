<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\Costing;
use App\Models\Part;
use App\Service\CostingService;
use App\Service\PartService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class CostingController extends Controller
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
    public function index(Request $request)
    {
        try {
            $costings = $this->costingService->getAllCostings();
            return view('sections.costing.index', ["costings" => $costings, "success" => $request->success, "error" => $request->error]);
        } catch (\Exception $e) {
            return view('sections.part.index', ["error" => $e->getMessage()]);
        }

    }

    /**
     * Load the new costing
     * @return View
     */
    public function _new()
    {
        return view('sections.costing.form', ["action" => "New"]);
    }

    /**
     * Handle creating new costing.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'total_invoice_rmb' => 'required|numeric|min:0.01',
                'exchange_rate' => 'required|numeric|min:0.01',
                'shipping_charges' => 'required|numeric|min:0.01',
                'total_freight' => 'required|numeric|min:0.01',
            ]);
            if ($validator->fails()) {
                return view('sections.costing.form', ["action" => "New", "error" => $validator->errors()->first()]);
            }

            $costing = $this->costingService->createCosting($request->total_invoice_rmb, $request->exchange_rate, $request->shipping_charges, $request->total_freight);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Failed to create new costing.",
                ]);
            }

            return redirect()->route('costings.show', [
                'id' => $costing->id,
                'success' => "Drafted new costing successfully.",
            ]);

        } catch (\Exception $e) {
            return view('sections.costing.form', ["action" => "New", "error" => $e->getMessage()]);
        }
    }

    /**
     * Load the show costing
     * @param int $id
     *
     * @return View
     */
    public function _show(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                abort(404);
            }

            $costing = $this->costingService->getCostingById($id);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Costing cannot be found, invalid costing id.",
                ]);
            }

            $parts = $this->costingService->getCostingParts($costing);

            $data = (object)[
                "total_invoice_rmb" => $costing->total_invoice_rmb,
                "exchange_rate" => $costing->exchange_rate,
                "shipping_charges" => $costing->shipping_charges,
                "total_freight" => $costing->total_freight,
                "id" => $costing->id,
                "created_at" => $costing->created_at->toDayDateTimeString(),
                "created_at_ago" => $costing->created_at->diffForHumans(),
                "updated_at" => $costing->updated_at->diffForHumans(),
                "parts" => $parts->list,
                "net_selling_price" => $parts->net_selling_price,
                "net_profit" => $parts->net_profit,
                "total_quantity" => $parts->total_quantity
            ];

            return view('sections.costing.form', ["action" => "Edit", "data" => $data, "error" => $request->error, "success" => $request->success]);
        } catch (\Exception $e) {
            return redirect()->route('costings', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle updating existing costing.
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'total_invoice_rmb' => 'required|numeric|min:0.01',
                'exchange_rate' => 'required|numeric|min:0.01',
                'shipping_charges' => 'required|numeric|min:0.01',
                'total_freight' => 'required|numeric|min:0.01',
            ]);
            if ($validator->fails()) {
                return view('sections.costing.form', ["action" => "New", "error" => $validator->errors()->first()]);
            }

            $costing = $this->costingService->getCostingById($id);
            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Costing cannot be found, invalid costing id.",]);
            }
            $costing = $this->costingService->updateCosting($costing, $request->total_invoice_rmb, $request->exchange_rate, $request->shipping_charges, $request->total_freight);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Failed to update costing.",
                ]);
            }

            return redirect()->route('costings.show', [
                'id' => $costing->id,
                'success' => "Costing details updated successfully.",
            ]);

        } catch (\Exception $e) {
            return redirect()->route('costings.show', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Load the new part costing
     * @param int $id
     *
     * @return View
     */
    public function _newPart(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                abort(404);
            }
            $costing = $this->costingService->getCostingById($id);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Costing cannot be found, invalid costing id.",
                ]);
            }

            $alreadyExistingParts = $costing->parts->pluck('id')->toArray();

            $parts = $this->partService->getAllParts($alreadyExistingParts);

            $data = (object)[
                "total_invoice_rmb" => $costing->total_invoice_rmb,
                "exchange_rate" => $costing->exchange_rate,
                "shipping_charges" => $costing->shipping_charges,
                "total_freight" => $costing->total_freight,
                "transport_charge_china" => $costing->transportChargeChina(),
                "total_sum_in_LKR" => $costing->totalSumInLKR(),
                "id" => $costing->id,
                "parts" => $parts,
                "new_part" => (int)$request->new_part_id ?? null
            ];
            return view('sections.costing.parts', ["action" => "New", "data" => $data, "error" => $request->error, "success" => $request->success]);
        } catch (\Exception $e) {
            return redirect()->route('costings.show', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle creating new part costing.
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function storePartCosting(Request $request, $id)
    {
        try {

            if (!is_numeric($id)) {
                abort(404);
            }
            $validator = Validator::make($request->all(), [
                'part' => 'required|integer|min:1|exists:parts,id',
                'unit_cost' => 'required|numeric|min:0.01',
                'quantity' => 'required|numeric|min:0.01',
                'margin' => 'required|numeric|min:0.01',
                'selling_margin' => 'required|numeric|min:0.01',
                'selling_margin_percentage' => 'nullable|in:on',
            ]);

            if ($validator->fails()) {
                return redirect()->route('costings.part.new', ["id" => $id, "action" => "New", "error" => $validator->errors()->first()]);
            }

            $costing = $this->costingService->getCostingById($id);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Costing cannot be found, invalid costing id.",
                ]);
            }

            $partCosting = $this->costingService->storePart($costing, $request->part, $request->unit_cost, $request->quantity, $request->margin, $request->selling_margin, (bool)$request->selling_margin_percentage);

            if (!$partCosting) {
                return redirect()->route('costings.show', [
                    'id' => $costing->id,
                    'error' => "Failed to create new part costing.",
                ]);
            }
            return redirect()->route('costings.show', [
                'id' => $costing->id,
                'success' => "New part costing added successfully.",
            ]);

        } catch (\Exception $e) {
            return redirect()->route('costings.part.new', ["id" => $id, "action" => "New", "error" => $e->getMessage()]);
        }
    }

    /**
     * Load the existing part costing
     * @param int $id
     *
     * @return View
     */
    public function _editPart(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                abort(404);
            }
            $costing = $this->costingService->getCostingById($id);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Costing cannot be found, invalid costing id.",
                ]);
            }

            $validator = Validator::make($request->all(), [
                'part_id' => 'required|integer|min:1|exists:parts,id'
            ]);

            if ($validator->fails()) {
                return redirect()->route('costings', [
                    'error' => $validator->errors()->first(),
                ]);
            }

            $part_id = (int)$request->part_id;

            //Will be pulling the existing part_id
            $alreadyExistingParts = array_diff($costing->parts->pluck('id')->toArray(), [$part_id]);

            $parts = $this->partService->getAllParts($alreadyExistingParts);

            $editingPart = $this->costingService->getPartCostingByPartId($costing, $part_id);

            if (!(array)$editingPart) {
                return redirect()->route('costings.show', [
                    'id' => $costing->id,
                    'error' => "Invalid part id selected, part id does not exists",
                ]);
            }

            $data = (object)[
                "total_invoice_rmb" => $costing->total_invoice_rmb,
                "exchange_rate" => $costing->exchange_rate,
                "shipping_charges" => $costing->shipping_charges,
                "total_freight" => $costing->total_freight,
                "transport_charge_china" => $costing->transportChargeChina(),
                "total_sum_in_LKR" => $costing->totalSumInLKR(),
                "id" => $costing->id,
                "parts" => $parts,
                "part" => $editingPart,
            ];
            return view('sections.costing.parts', ["action" => "Edit", "data" => $data, "error" => $request->error, "success" => $request->success]);
        } catch (\Exception $e) {
            return redirect()->route('costings.show', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle creating new part costing.
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function updatePartCosting(Request $request, $id)
    {
        try {

            if (!is_numeric($id)) {
                abort(404);
            }
            $validator = Validator::make($request->all(), [
                'old_part_id' => 'required|integer|min:1|exists:parts,id',
                'part' => 'required|integer|min:1|exists:parts,id',
                'unit_cost' => 'required|numeric|min:0.01',
                'quantity' => 'required|numeric|min:0.01',
                'margin' => 'required|numeric|min:0.01',
                'selling_margin' => 'required|numeric|min:0.01',
                'selling_margin_percentage' => 'nullable|in:on',
            ]);

            if ($validator->fails()) {
                return redirect()->route('costings.part.new', ["id" => $id, "action" => "New", "error" => $validator->errors()->first()]);
            }

            $costing = $this->costingService->getCostingById($id);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Costing cannot be found, invalid costing id.",
                ]);
            }

            $partCosting = $this->costingService->updatePart($costing, $request->old_part_id, $request->part, $request->unit_cost, $request->quantity, $request->margin, $request->selling_margin, (bool)$request->selling_margin_percentage);

            if (!$partCosting) {
                return redirect()->route('costings.show', [
                    'id' => $costing->id,
                    'error' => "Failed to update part costing.",
                ]);
            }

            return redirect()->route('costings.show', [
                'id' => $costing->id,
                'success' => "Part costing updated successfully.",
            ]);

        } catch (\Exception $e) {
            return redirect()->route('costings', ["error" => $e->getMessage()]);
        }
    }

    /**
     * Delete part costing
     *
     * @param $id
     * @param $partId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePartCosting($id, $partId)
    {
        try {
            if (!is_numeric($id) || !is_numeric($partId)) {
                abort(404);
            }
            $costing = $this->costingService->getCostingById($id);

            if (!$costing) {
                return redirect()->route('costings', [
                    'error' => "Costing cannot be found, invalid costing id.",
                ]);
            }

            $this->costingService->deletePart($costing, $partId);

            return redirect()->route('costings.show', [
                'id' => $costing->id,
                'success' => "Part costing deleted successfully.",
            ]);


        } catch (\Exception $e) {
            return redirect()->route('costings', ["error" => $e->getMessage()]);
        }
    }
}
