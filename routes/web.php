<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PerdinController;
use App\Http\Controllers\SheetTemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/master-data/suggestions', [MasterDataController::class, 'suggestions'])->name('master-data.suggestions');
Route::get('/master-data/pegawai', [MasterDataController::class, 'searchPegawai'])->name('master-data.pegawai');

Route::get('/template-settings', [SheetTemplateController::class, 'index'])->name('template-settings.index');
Route::put('/template-settings/{perdinSheetTemplate}', [SheetTemplateController::class, 'update'])->name('template-settings.update');

Route::prefix('perdin')->name('perdin.')->group(function () {
    Route::post('/', [PerdinController::class, 'store'])->name('store');
    Route::get('/{perdin}/preview', [PerdinController::class, 'preview'])->name('preview');
    Route::get('/{perdin}/excel', [PerdinController::class, 'generateExcel'])->name('excel');
    Route::get('/{perdin}/pdf', [PerdinController::class, 'generatePdf'])->name('pdf');
    Route::delete('/{perdin}', [PerdinController::class, 'destroy'])->name('destroy');
});