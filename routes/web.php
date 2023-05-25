<?php

use App\Http\Controllers\CashMachineController;
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

Route::get('/', [CashMachineController::class, 'index'])->name('home');

Route::prefix('transaction')->name('transaction.')->group(function () {
    Route::get('/{type}', [CashMachineController::class, 'transaction'])->name('type');
    Route::post('store', [CashMachineController::class, 'store'])->name('store');
    Route::get('show/{transaction}', [CashMachineController::class, 'show'])->name('show');
    Route::get('error', [CashMachineController::class, 'transactionError'])->name('error');
});
