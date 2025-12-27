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
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/admin', function () {
    return Inertia::render('Admin/Applications'); 
})->name('admin');

Route::get('/owners.index', function () {
    // $owner = Owner::first(); // or auth()->user()->owner, etc.
    return Inertia::render('Owners/Index');
})->name('owners.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        // Get admins with roles
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->with(['roles'])->get();

        // Get owners with their user relation
        $owners = Owner::with('user')->get();

        // Send both to Dashboard.vue
        return Inertia::render('Dashboard', [
            'admins' => $admins,
            'owners' => $owners,
        ]);
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
    | Admins (Super Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:superadmin'])->group(function () {
        Route::resource('admins', AdminController::class);
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
   Route::middleware('auth')->group(function () {
    // Store business information
    Route::post('/media-entities', [MediaEntityController::class, 'store'])->name('media-entities.store');
    
    // Get owner's media entities
    Route::get('/api/owner/media-entities', [MediaEntityController::class, 'getOwnerMediaEntities'])->name('owner.media-entities');
});
   
    /*
    |--------------------------------------------------------------------------
    | Applications
    |--------------------------------------------------------------------------
    */
    Route::get('applications', [ApplicationController::class, 'index'])->middleware('role:admin|superadmin');
    Route::get('applications/mine', [ApplicationController::class, 'myApplications'])->middleware('role:owner');
    Route::get('applications/{id}', [ApplicationController::class, 'show'])->middleware('role:admin|owner|superadmin');
    Route::post('applications', [ApplicationController::class, 'store'])->middleware('role:owner');
    Route::put('applications/{id}', [ApplicationController::class, 'update'])->middleware('role:owner');
    Route::delete('applications/{id}', [ApplicationController::class, 'destroy'])->middleware('role:owner|admin|superadmin');
    Route::post('applications/{id}/submit', [ApplicationController::class, 'submit'])->middleware('role:owner');

    /*
    |--------------------------------------------------------------------------
    | Application Documents
    |--------------------------------------------------------------------------
    */
    Route::get('applications/{id}/documents', [ApplicationDocumentController::class, 'index'])->middleware('role:admin|owner|superadmin');
    Route::post('applications/{id}/documents', [ApplicationDocumentController::class, 'store'])->middleware('role:owner');
    Route::delete('documents/{id}', [ApplicationDocumentController::class, 'destroy'])->middleware('role:owner|admin|superadmin');

    /*
    |--------------------------------------------------------------------------
    | Application Reviews
    |--------------------------------------------------------------------------
    */
    Route::post('applications/{id}/review', [ApplicationReviewController::class, 'store'])->middleware('role:reviewer|admin|superadmin');
    Route::get('applications/{id}/reviews', [ApplicationReviewController::class, 'index'])->middleware('role:admin|reviewer|owner|superadmin');

    /*
    |--------------------------------------------------------------------------
    | Licenses
    |--------------------------------------------------------------------------
    */

    // Admin/Super Admin only routes
    Route::get('licenses', [LicenseController::class, 'index'])->name('licenses.index');
    Route::get('licenses/mine', [LicenseController::class, 'myLicenses'])->name('licenses.mine');
    Route::get('licenses/{id}', [LicenseController::class, 'show']);
    Route::post('licenses', [LicenseController::class, 'store'])->name('licenses.store');
    Route::put('licenses/{id}/status', [LicenseController::class, 'updateStatus'])->name('licenses.update-status');
    Route::delete('licenses/{id}', [LicenseController::class, 'destroy']);
    Route::put('licenses/{id}', [LicenseController::class, 'update']);    

    /*
    |--------------------------------------------------------------------------
    | Search (Laravel Scout)
    |--------------------------------------------------------------------------
    */
    Route::get('search', [SearchController::class, 'global'])->middleware('role:admin|superadmin');
    Route::get('search/entities', [SearchController::class, 'entities'])->middleware('role:admin|superadmin');
    Route::get('search/owners', [SearchController::class, 'owners'])->middleware('role:admin|superadmin');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::get('reports/licenses', [ReportController::class, 'licenses'])->middleware('role:admin|superadmin');
    Route::get('reports/applications', [ReportController::class, 'applications'])->middleware('role:admin|superadmin');
    Route::get('reports/renewals', [ReportController::class, 'renewals'])->middleware('role:admin|superadmin');

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-read', [NotificationController::class, 'markRead']);
});