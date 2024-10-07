<?php

namespace App\Service;

use App\Helpers\NumberHelper;
use App\Models\Costing;
use App\Models\Part;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class CostingService
{

    private $numberHelper;

    public function __construct(NumberHelper $numberHelper)
    {
        $this->numberHelper = $numberHelper;
    }

    /**
     * Retrieve all costings
     * @return Collection
     */
    public function getAllCostings(): Collection
    {
        return Costing::orderBy('created_at', 'desc')->get();
    }

    /**
     * Create new costing
     * @param float $total_invoice_rmb
     * @param float $exchange_rate
     * @param float $shipping_charges
     * @param float $total_freight
     * @return Costing
     */
    public function createCosting(float $total_invoice_rmb, float $exchange_rate, float $shipping_charges, float $total_freight): Costing
    {
        $costing = new Costing();
        $costing->total_invoice_rmb = $total_invoice_rmb;
        $costing->exchange_rate = $exchange_rate;
        $costing->shipping_charges = $shipping_charges;
        $costing->total_freight = $total_freight;
        $costing->status = 1;
        $costing->save();

        return $costing;
    }

    /**
     * Get costing object by id
     * @param int $id
     * @return Costing
     */
    public function getCostingById(int $id): Costing
    {
        return Costing::find($id);
    }

    /**
     * Update existing costing
     * @param Costing $costing
     * @param float $total_invoice_rmb
     * @param float $exchange_rate
     * @param float $shipping_charges
     * @param float $total_freight
     * @return Costing
     */
    public function updateCosting(Costing $costing, float $total_invoice_rmb, float $exchange_rate, float $shipping_charges, float $total_freight): Costing
    {
        $costing->total_invoice_rmb = $total_invoice_rmb;
        $costing->exchange_rate = $exchange_rate;
        $costing->shipping_charges = $shipping_charges;
        $costing->total_freight = $total_freight;
        $costing->status = 1;
        $costing->save();

        return $costing;
    }


    /**
     * Get all the parts costing of a costing
     * @param Costing $costing
     * @return object
     */
    public function getCostingParts(Costing $costing): object
    {
        $parts = collect([]);
        foreach ($costing->parts as $part) {
            $parts->push((object)[
                "id" => $part->id,
                "title" => Str::limit($part->title, 20, '...'),
                "part_no" => $part->part_no,
                "quantity" => $part->pivot->quantity,
                "unit_cost" => $part->pivot->unit_cost,

                //Retrieve all the calculated pricings
                ...$this->retrieveCostings($part->pivot, $costing)
            ]);
        }

        $total_quantity = $parts->sum('quantity');
        $net_selling_price = $parts->sum('_unformatted_selling_price');
        $net_profit = $this->numberHelper->formatToPricing($net_selling_price - $parts->sum('_unformatted_cost_before_margin'));
        $net_selling_price = $this->numberHelper->formatToPricing($net_selling_price);

        return (object)["total_quantity" => $total_quantity, "net_selling_price" => $net_selling_price, "net_profit" => $net_profit, "list" => $parts];
    }

    /**
     * Calculate the costings for the specific pivot part
     * @param Pivot $pivotPart
     * @param Costing $costing
     * @return array
     */
    private function retrieveCostings(Pivot $pivotPart, Costing $costing): array
    {
        $exchange_rate = $costing->exchange_rate;
        $total_sum_in_LKR = $costing->totalSumInLKR();
        $transport_charge_china = $costing->transportChargeChina();
        $total_freight = $costing->total_freight;

        $quantity = $pivotPart->quantity;
        $unit_cost = $pivotPart->unit_cost;
        $margin = $pivotPart->margin;
        $selling_margin = $pivotPart->selling_margin;
        $selling_margin_percentage = $pivotPart->selling_margin_percentage;

        $total_RMB = $this->calculateTotalRMB($unit_cost, $quantity);
        $total_LKR = $this->calculateTotalLKR($total_RMB, $exchange_rate);

        $freight_per_unit = $this->calculateFreightPerUnit($transport_charge_china, $total_freight, $total_LKR, $total_sum_in_LKR);

        $cost_before_margin = $this->calculateCostBeforeMargin($quantity, $total_LKR, $freight_per_unit);

        $cost_after_margin = $this->calculateCostAfterMargin($cost_before_margin, $margin);

        $selling_price = $this->calculateSellingPrice($cost_after_margin, $selling_margin, $selling_margin_percentage);

        return [
            "total_RMB" => $this->numberHelper->formatToPricing($total_RMB),
            "total_LKR" => $this->numberHelper->formatToPricing($total_LKR),
            "freight_per_unit" => $this->numberHelper->formatToPricing($freight_per_unit),
            "cost_before_margin" => $this->numberHelper->formatToPricing($cost_before_margin),
            "_unformatted_cost_before_margin" => $cost_before_margin,
            "cost_after_margin" => $this->numberHelper->formatToPricing($cost_after_margin),
            "selling_price" => $this->numberHelper->formatToPricing($selling_price),
            "_unformatted_selling_price" => $selling_price
        ];
    }

    /**
     * Calculate total RMB for pivot part
     * @param float $unit_cost
     * @param float $qunatity
     * @return float
     */
    private function calculateTotalRMB(float $unit_cost, float $quantity): float
    {
        return $unit_cost * $quantity;
    }

    /**
     * Calculate total in LKR for pivot part
     * @param float $total_RMB
     * @param float $exchange_rate
     * @return float
     */
    private function calculateTotalLKR(float $total_RMB, float $exchange_rate): float
    {
        return $total_RMB * $exchange_rate;
    }

    /**
     * Calculate total freight per unit
     *
     * @param float $transport_charge_china
     * @param float $total_freight
     * @param float $total_LKR
     * @param float $total_sum_in_LKR
     * @return float
     */
    private function calculateFreightPerUnit(float $transport_charge_china, float $total_freight, float $total_LKR, float $total_sum_in_LKR): float
    {
        return ($transport_charge_china + $total_freight) * ($total_LKR / $total_sum_in_LKR);
    }

    /**
     * Calculate cost before margin
     * @param float $quantity
     * @param float $total_LKR
     * @param float $freight_per_unit
     * @return float
     */
    private function calculateCostBeforeMargin(float $quantity, float $total_LKR, float $freight_per_unit): float
    {
        return $freight_per_unit + ($total_LKR / $quantity);
    }

    /**
     * Calculate cost after margin
     * @param float $cost_before_margin
     * @param float $margin
     * @return float
     */
    private function calculateCostAfterMargin(float $cost_before_margin, float $margin): float
    {
        return $cost_before_margin + ($cost_before_margin * ($margin / 100));
    }

    /**
     * Calculate the final selling price
     * @param float $cost_after_margin
     * @param float $selling_margin
     * @param bool $selling_margin_percentage
     * @return float
     */
    private function calculateSellingPrice(float $cost_after_margin, float $selling_margin, bool $selling_margin_percentage): float
    {
        if ($selling_margin_percentage) {
            return $cost_after_margin + ($cost_after_margin * ($selling_margin / 100));
        }
        return $cost_after_margin + $selling_margin;
    }

    /**
     * Get part costing of a specific part from part id
     * @param Costing $costing
     * @param int $part_id
     * @return object
     */
    public function getPartCostingByPartId(Costing $costing, int $part_id): object
    {
        $part = $costing->parts()->wherePivot('part_id', $part_id)->first();
        if (!$part) return (object)[];
        return (object)[
            "id" => $part_id,
            "unit_cost" => $part->pivot->unit_cost,
            "quantity" => $part->pivot->quantity,
            "margin" => $part->pivot->margin,
            "selling_margin" => $part->pivot->selling_margin,
            "selling_margin_percentage" => (bool)$part->pivot->selling_margin_percentage
        ];
    }

    /**
     * Create new part costing
     * @param Costing $costing
     * @param int $part_id
     * @param float $unit_cost
     * @param float $quantity
     * @param float $margin
     * @param float $selling_margin
     * @param bool $selling_margin_percentage
     * @return bool
     */
    public function storePart(Costing $costing, int $part_id, float $unit_cost, float $quantity, float $margin, float $selling_margin, bool $selling_margin_percentage): bool
    {
        $costing->parts()->attach([$part_id => [
            'unit_cost' => $unit_cost,
            'quantity' => $quantity,
            'margin' => $margin,
            'selling_margin' => $selling_margin,
            'selling_margin_percentage' => $selling_margin_percentage
        ]]);
        $costing->touch();
        return true;
    }

    /**
     * Update existing part costing
     * @param Costing $costing
     * @param int $old_part_id
     * @param int $part_id
     * @param float $unit_cost
     * @param float $quantity
     * @param float $margin
     * @param float $selling_margin
     * @param bool $selling_margin_percentage
     * @return bool
     */
    public function updatePart(Costing $costing, int $old_part_id, int $part_id, float $unit_cost, float $quantity, float $margin, float $selling_margin, bool $selling_margin_percentage): bool
    {
        if ($old_part_id === $part_id) {
            $costing->parts()->syncWithoutDetaching([$part_id => [
                'unit_cost' => $unit_cost,
                'quantity' => $quantity,
                'margin' => $margin,
                'selling_margin' => $selling_margin,
                'selling_margin_percentage' => (bool)$selling_margin_percentage
            ]]);
            $costing->touch();
            return true;
        } else {
            $costing->parts()->detach($old_part_id);
            return $this->storePart($costing, $part_id, $unit_cost, $quantity, $margin, $selling_margin, $selling_margin_percentage);
        }
    }

    /**
     * Delete part costing
     * @param Costing $costing
     * @param int $part_id
     * @return bool
     */
    public function deletePart(Costing $costing, int $part_id): bool
    {
        $costing->parts()->detach($part_id);
        $costing->touch();
        return true;
    }
}
