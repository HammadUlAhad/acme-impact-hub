<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Campaign routes
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/approve', [CampaignController::class, 'approve'])->name('campaigns.approve');
    Route::post('campaigns/{campaign}/feature', [CampaignController::class, 'toggleFeature'])->name('campaigns.feature');

    // Donation routes
    Route::get('donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('campaigns/{campaign}/donate', [DonationController::class, 'create'])->name('donations.create');
    Route::post('campaigns/{campaign}/donate', [DonationController::class, 'store'])->name('donations.store');
    Route::get('donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    Route::post('donations/{donation}/approve', [DonationController::class, 'approve'])->name('donations.approve');
    Route::post('donations/{donation}/refund', [DonationController::class, 'refund'])->name('donations.refund');

    // Admin routes
    Route::middleware(['role:admin|campaign_manager'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });
});

require __DIR__.'/settings.php';
