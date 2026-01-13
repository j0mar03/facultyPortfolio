<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard - Role-based
    Route::get('/dashboard', function (Request $request) {
        $user = Auth::user();
        if ($user->role === 'faculty') {
            return app(\App\Http\Controllers\Faculty\DashboardController::class)->index();
        } elseif ($user->role === 'chair') {
            return app(\App\Http\Controllers\Chair\DashboardController::class)->index($request);
        } elseif (in_array($user->role, ['admin', 'auditor'])) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // My Class Offerings (Faculty)
    Route::get('/my-class-offerings', function () {
        return view('faculty.class-offerings');
    })->name('faculty.class-offerings');

    Route::resource('portfolios', \App\Http\Controllers\PortfolioController::class)
        ->only(['index', 'store', 'show']);

    Route::post('/portfolios/{portfolio}/submit', [\App\Http\Controllers\PortfolioController::class, 'submit'])
        ->name('portfolios.submit');

    Route::post('/portfolios/{portfolio}/items', [\App\Http\Controllers\PortfolioItemController::class, 'store'])
        ->name('portfolio-items.store');

    Route::get('/portfolios/{portfolio}/items/{item}/download', [\App\Http\Controllers\PortfolioItemController::class, 'download'])
        ->name('portfolio-items.download');
    
    Route::get('/portfolios/{portfolio}/items/{item}/preview', [\App\Http\Controllers\PortfolioItemController::class, 'preview'])
        ->name('portfolio-items.preview');

    Route::post('/portfolios/{portfolio}/items/{item}/update', [\App\Http\Controllers\PortfolioItemController::class, 'update'])
        ->name('portfolio-items.update');

    Route::delete('/portfolios/{portfolio}/items/{item}', [\App\Http\Controllers\PortfolioItemController::class, 'destroy'])
        ->name('portfolio-items.destroy');

    // Review routes (Chair/Admin)
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReviewController::class, 'index'])
            ->name('index');
        Route::get('/{portfolio}', [\App\Http\Controllers\ReviewController::class, 'show'])
            ->name('show');
        Route::post('/{portfolio}/decision', [\App\Http\Controllers\ReviewController::class, 'decision'])
            ->name('decision');
    });

    // Chair routes
    Route::prefix('chair')->name('chair.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Chair\DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/subjects', [\App\Http\Controllers\Chair\SubjectController::class, 'index'])
            ->name('subjects.index');
        Route::get('/subjects/{subject}', [\App\Http\Controllers\Chair\SubjectController::class, 'show'])
            ->name('subjects.show');
        Route::post('/subjects/{subject}/assign', [\App\Http\Controllers\Chair\SubjectController::class, 'assignFaculty'])
            ->name('subjects.assign');
        Route::delete('/subjects/assignments/{classOffering}', [\App\Http\Controllers\Chair\SubjectController::class, 'removeAssignment'])
            ->name('subjects.remove-assignment');
        Route::get('/subjects/assignments/{classOffering}/download', [\App\Http\Controllers\Chair\SubjectController::class, 'downloadAssignment'])
            ->name('subjects.download-assignment');
        Route::post('/subjects/assignments/{classOffering}/upload', [\App\Http\Controllers\Chair\SubjectController::class, 'uploadAssignment'])
            ->name('subjects.upload-assignment');
        Route::post('/subjects/documents/{classOffering}/{type}', [\App\Http\Controllers\Chair\SubjectController::class, 'uploadDocument'])
            ->name('subjects.upload-document');
        Route::get('/subjects/documents/{classOffering}/{type}', [\App\Http\Controllers\Chair\SubjectController::class, 'downloadDocument'])
            ->name('subjects.download-document');

        // Reports routes
        Route::get('/reports', [\App\Http\Controllers\Chair\ReportController::class, 'index'])
            ->name('reports.index');
        Route::get('/reports/download-all', [\App\Http\Controllers\Chair\ReportController::class, 'downloadAll'])
            ->name('reports.download-all');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])
            ->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])
            ->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])
            ->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
            ->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
            ->name('users.destroy');

        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('reports.index');
        Route::get('/reports/{portfolio}/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])
            ->name('reports.export');

        Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index'])
            ->name('courses.index');

        Route::get('/import/class-offerings', [\App\Http\Controllers\Admin\ClassOfferingImportController::class, 'showForm'])
            ->name('import.class-offerings');
        Route::post('/import/class-offerings', [\App\Http\Controllers\Admin\ClassOfferingImportController::class, 'import'])
            ->name('import.class-offerings.store');
    });
});
