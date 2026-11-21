<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\InboxController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // límite de intentos
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/home', HomeController::class)->name('home');

    // Administrador
    Route::middleware(EnsureRole::class.':admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::get('catalogs', [CatalogController::class, 'index'])->name('catalogs');
        Route::post('specialties', [CatalogController::class, 'storeSpecialty'])->name('specialties.store');
        Route::post('specialties/{specialty}/toggle', [CatalogController::class, 'toggleSpecialty'])->name('specialties.toggle');
        Route::post('sites', [CatalogController::class, 'storeSite'])->name('sites.store');
        Route::get('audit', [AuditController::class, 'index'])->name('audit');
    });

    // SPRINT-4-START
    // Médico general
    Route::middleware(EnsureRole::class.':general')->group(function () {
        Route::get('consultations', [ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('consultations', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    });

    // Médico especialista
    Route::middleware(EnsureRole::class.':specialist')->prefix('inbox')->name('inbox.')->group(function () {
        Route::get('/', [InboxController::class, 'index'])->name('index');
        Route::get('{consultation}', [InboxController::class, 'show'])->name('show');
        Route::post('{consultation}/respond', [InboxController::class, 'respond'])->name('respond');
    });

    // SPRINT-4-END

    // SPRINT-5-START
    // Coordinador
    Route::middleware(EnsureRole::class.':coordinator')->prefix('indicators')->name('indicators.')->group(function () {
        Route::get('/', [IndicatorController::class, 'index'])->name('index');
        Route::get('export', [IndicatorController::class, 'export'])->name('export');
    });
    // SPRINT-5-END
});
