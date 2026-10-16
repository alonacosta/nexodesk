<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::patch('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('companies/{company:slug}', [CompanyController::class, 'show'])->name('companies.show');
    Route::patch('companies/{company:slug}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('companies/{company:slug}', [CompanyController::class, 'destroy'])->name('companies.destroy');
});

require __DIR__.'/settings.php';
