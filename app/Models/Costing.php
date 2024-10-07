<?php

namespace App\Models;

use App\Utils\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Costing extends Model
{
    use HasFactory;
    protected $table="costings";

    protected $fillable = [
        'total_invoice_rmb',
        'exchange_rate',
        'shipping_charges',
        'total_freight',
        'status',
    ];

    protected $appends=['costing_status'];

    /**
     * Get the parts associated with the costing.
     */
    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class)->withPivot(['id','unit_cost', 'quantity','margin','selling_margin','selling_margin_percentage']);
    }

    public function getCostingStatusAttribute(){
        return Constant::$costingStatus[array_search($this->attributes['status'], array_column(Constant::$costingStatus, 'key'))]['value'];
    }

    public function totalSumInLKR() : float{
        return $this->attributes['total_invoice_rmb']*$this->attributes['exchange_rate'];
    }

    public function transportChargeChina(): float{
        return $this->attributes['exchange_rate']*$this->attributes['shipping_charges'];
    }
}
