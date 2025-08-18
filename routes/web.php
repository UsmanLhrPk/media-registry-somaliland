<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationDocumentController;
use App\Http\Controllers\ApplicationReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\MediaEntityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/admin', function () {
    return Inertia::render('Admin/Applications'); 
})->name('admin');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    // Authenticated User
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    /*
    |--------------------------------------------------------------------------
    | Owners (Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->group(function () {
        Route::resource('owners', OwnerController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Media Entities
    |--------------------------------------------------------------------------
    */
    Route::get('media-entities', [MediaEntityController::class, 'index'])->middleware('role:admin|reviewer');
    Route::get('media-entities/mine', [MediaEntityController::class, 'myEntities'])->middleware('role:owner');
    Route::get('media-entities/{id}', [MediaEntityController::class, 'show'])->middleware('role:admin|reviewer|owner');
    Route::post('media-entities', [MediaEntityController::class, 'store'])->middleware('role:owner|admin');
    Route::put('media-entities/{id}', [MediaEntityController::class, 'update'])->middleware('role:owner|admin');
    Route::delete('media-entities/{id}', [MediaEntityController::class, 'destroy'])->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | Applications
    |--------------------------------------------------------------------------
    */
    Route::get('applications', [ApplicationController::class, 'index'])->middleware('role:admin|reviewer');
    Route::get('applications/mine', [ApplicationController::class, 'myApplications'])->middleware('role:owner');
    Route::get('applications/{id}', [ApplicationController::class, 'show'])->middleware('role:admin|reviewer|owner');
    Route::post('applications', [ApplicationController::class, 'store'])->middleware('role:owner');
    Route::put('applications/{id}', [ApplicationController::class, 'update'])->middleware('role:owner');
    Route::delete('applications/{id}', [ApplicationController::class, 'destroy'])->middleware('role:owner|admin');
    Route::post('applications/{id}/submit', [ApplicationController::class, 'submit'])->middleware('role:owner');

    /*
    |--------------------------------------------------------------------------
    | Application Documents
    |--------------------------------------------------------------------------
    */
    Route::get('applications/{id}/documents', [ApplicationDocumentController::class, 'index'])->middleware('role:admin|reviewer|owner');
    Route::post('applications/{id}/documents', [ApplicationDocumentController::class, 'store'])->middleware('role:owner');
    Route::delete('documents/{id}', [ApplicationDocumentController::class, 'destroy'])->middleware('role:owner|admin');

    /*
    |--------------------------------------------------------------------------
    | Application Reviews
    |--------------------------------------------------------------------------
    */
    Route::post('applications/{id}/review', [ApplicationReviewController::class, 'store'])->middleware('role:reviewer|admin');
    Route::get('applications/{id}/reviews', [ApplicationReviewController::class, 'index'])->middleware('role:admin|reviewer|owner');

    /*
    |--------------------------------------------------------------------------
    | Licenses
    |--------------------------------------------------------------------------
    */
    Route::get('licenses', [LicenseController::class, 'index'])->middleware('role:admin|reviewer');
    Route::get('licenses/mine', [LicenseController::class, 'myLicenses'])->middleware('role:owner');
    Route::get('licenses/{id}', [LicenseController::class, 'show'])->middleware('role:admin|reviewer|owner');
    Route::post('licenses', [LicenseController::class, 'store'])->middleware('role:admin');
    Route::put('licenses/{id}', [LicenseController::class, 'update'])->middleware('role:admin');
    Route::delete('licenses/{id}', [LicenseController::class, 'destroy'])->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | Search (Laravel Scout)
    |--------------------------------------------------------------------------
    */
    Route::get('search', [SearchController::class, 'global'])->middleware('role:admin|reviewer');
    Route::get('search/entities', [SearchController::class, 'entities'])->middleware('role:admin|reviewer');
    Route::get('search/owners', [SearchController::class, 'owners'])->middleware('role:admin|reviewer');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::get('reports/licenses', [ReportController::class, 'licenses'])->middleware('role:admin');
    Route::get('reports/applications', [ReportController::class, 'applications'])->middleware('role:admin');
    Route::get('reports/renewals', [ReportController::class, 'renewals'])->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-read', [NotificationController::class, 'markRead']);
});
