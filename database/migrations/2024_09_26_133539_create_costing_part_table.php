<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('costing_part', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_id');
            $table->unsignedBigInteger('costing_id');
            $table->float('unit_cost');
            $table->integer('quantity');
            $table->float('margin');
            $table->float('selling_margin');
            $table->boolean('selling_margin_percentage')->default(false);

            $table->foreign('part_id')->references('id')->on('parts')->onDelete('cascade');
            $table->foreign('costing_id')->references('id')->on('costings')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_costing');
    }
};
