<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('portfolios', \App\Http\Controllers\PortfolioController::class)
        ->only(['index', 'store', 'show']);

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/import/class-offerings', [\App\Http\Controllers\Admin\ClassOfferingImportController::class, 'showForm'])
            ->name('import.class-offerings');
        Route::post('/import/class-offerings', [\App\Http\Controllers\Admin\ClassOfferingImportController::class, 'import'])
            ->name('import.class-offerings.store');
    });
});
