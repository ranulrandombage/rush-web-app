<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Part extends Model
{
    use HasFactory;

    protected $table = "parts";

    protected $fillable = [
        'title',
        'part_no',
    ];

    /**
     * Get the costings associated with the part.
     */
    public function costings(): BelongsToMany
    {
        return $this->belongsToMany(Costing::class)->withPivot(['id', 'unit_cost', 'quantity', 'margin', 'selling_margin', 'selling_margin_percentage']);
    }
}
