<?php

namespace App\Http\Controllers\Part;

use App\Http\Controllers\Controller;
use App\Models\Part;
use App\Service\PartService;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PartController extends Controller
{

    private $partService;

    public function __construct(PartService $partService)
    {

        $this->partService = $partService;
    }

    /**
     * Load the parts view
     * @param \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function _index(Request $request)
    {
        try {
            $parts = $this->partService->getAllParts();
            return view('sections.part.index', ["parts" => $parts, "success" => $request->success, "error" => $request->error]);
        } catch (\Exception $e) {
            return view('sections.part.index', ["error" => $e->getMessage()]);
        }

    }

    /**
     * Load the add part
     * @return View
     */
    public function _add()
    {
        return view('sections.part.form', ["action" => "Add"]);
    }

    /**
     * Handle creating new part.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'costing_id' => 'nullable|integer|min:1|exists:costings,id',
                'part_title' => 'required|string|max:255',
                'part_no' => 'required|string|max:255|unique:parts,part_no',
            ]);
            if ($validator->fails()) {
                return $request->costing_id ? redirect()->route('costings.part.new', [
                    "id" => $request->costing_id ?? null,
                    "error" => $validator->errors()->first()
                ]) : view('sections.part.form', ["action" => "Add", "error" => $validator->errors()->first()]);
            }
            $returnUrl = $request->costing_id ? 'costings.part.new' : 'parts';

            $part = $this->partService->createPart($request->part_title, $request->part_no);

            if (!$part) {
                return redirect()->route($returnUrl, [
                    "id" => $request->costing_id ?? null,
                    'error' => "Failed to add new part.",
                ]);
            }

            return redirect()->route($returnUrl, [
                "id" => $request->costing_id ?? null,
                "new_part_id" => $part->id,
                'success' => "Added new part successfully.",
            ]);

        } catch (\Exception $e) {
            return view('sections.part.form', ["action" => "Add", "error" => $e->getMessage()]);
        }
    }

    /**
     * Load the show part
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

            $part = $this->partService->getPartById($id);

            if (!$part) {
                return redirect()->route('parts', [
                    'error' => "Part cannot be found, invalid part id.",
                ]);
            }
            $data = (object)["part_title" => $part->title, "part_no" => $part->part_no, "id" => $part->id];

            return view('sections.part.form', ["action" => "Edit", "data" => $data, "error" => $request->error]);
        } catch (\Exception $e) {
            return view('sections.part.index', ["error" => $e->getMessage()]);
        }
    }

    /**
     * Handle updating existing part.
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                abort(404);
            }

            $validator = Validator::make($request->all(), [
                'part_title' => 'required|string|max:255',
                'part_no' => 'required|string|max:255|unique:parts,part_no,' . $id,
            ]);

            if ($validator->fails()) {
                return redirect()->route('parts.show', [
                    "id" => $id,
                    "error" => $validator->errors()->first()
                ]);
            }

            $part = $this->partService->getPartById($id);

            if (!$part) {
                return redirect()->route('parts', [
                    'error' => "Part cannot be found, invalid part id.",
                ]);
            }

            $part = $this->partService->updatePart($part, $request->part_title, $request->part_no);

            if (!$part) {
                return redirect()->route('parts', [
                    'error' => "Part failed to update.",
                ]);
            }

            return redirect()->route('parts', [
                'success' => "Part updated successfully.",
            ]);

        } catch (\Exception $e) {
            return view('sections.part.form', ["action" => "Add", "error" => $e->getMessage()]);
        }
    }


}
