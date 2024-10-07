<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Costing\CostingController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Part\PartController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Login
Route::prefix('login')->group(function () {
    Route::get('/',  [LoginController::class, 'index'])->name('login');
    Route::post('authenticate',  [LoginController::class, '_authenticate'])->name('login.authenticate');
});

Route::get('logout',  [LoginController::class, '_logout'])->name('logout');

Route::group(["middleware" => 'auth'], function () {

    //Dashboard
    Route::get('/',  [DashboardController::class, '_index'])->name('dashboard');

    //Part
    Route::prefix('parts')->group(function () {
        Route::get('/',  [PartController::class, '_index'])->name('parts');
        Route::get('/add',  [PartController::class, '_add'])->name('parts.add');
        Route::post('/store',  [PartController::class, 'store'])->name('parts.store');
        Route::get('/show/{id}',  [PartController::class, '_show'])->name('parts.show');
        Route::put('/update/{id}',  [PartController::class, 'update'])->name('parts.update');
    });

    //Costing
    Route::prefix('costing')->group(function () {
        Route::get('/',  [CostingController::class, 'index'])->name('costings');
        Route::get('/new',  [CostingController::class, '_new'])->name('costings.new');
        Route::post('/store',  [CostingController::class, 'store'])->name('costings.store');
        Route::get('/show/{id}',  [CostingController::class, '_show'])->name('costings.show');
        Route::put('/update/{id}',  [CostingController::class, 'update'])->name('costings.update');
        Route::get('/{id}/part/new',  [CostingController::class, '_newPart'])->name('costings.part.new');
        Route::post('/{id}/part/store',  [CostingController::class, 'storePartCosting'])->name('costings.part.store');
        Route::get('/{id}/part/edit',  [CostingController::class, '_editPart'])->name('costings.part.edit');
        Route::put('/{id}/part/update',  [CostingController::class, 'updatePartCosting'])->name('costings.part.update');
        Route::delete('/{id}/delete/{partId}',  [CostingController::class, 'deletePartCosting'])->name('costings.part.delete');
    });
});


