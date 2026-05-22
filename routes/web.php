<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/impersonate/leave', [\App\Http\Controllers\Admin\UserController::class, 'leaveImpersonation'])->name('impersonate.leave');

    // Admin Routes group
    Route::prefix('admin')->name('admin.')->group(function () {

        // We use a fallback layout controller pattern or an explicit check inside a nested group callback
        Route::group([], function () {
            // Before compiling routes, Laravel evaluates groups. 
            // However, to execute code on the request, we should hook into the request lifecycle.
            // Let's create a clean inline group mapping that bypasses the broken middleware mechanism entirely:
        });
    });
});

// A cleaner, fail-proof approach using a proper custom middleware definition inside bootstrap/app.php is standard,
// but to keep it strictly within this file without crashing, we can use an inline controller filter:

Route::middleware(['auth'])->group(function () {
    Route::get('/impersonate/leave', [\App\Http\Controllers\Admin\UserController::class, 'leaveImpersonation'])->name('impersonate.leave');

    // Safe Admin Check: Instead of a closure middleware, we filter the request gracefully inside a nested controller execution layout, 
    // or simply register a localized alias. Let's use standard routing without inline middleware arrays:
    Route::prefix('admin')->name('admin.')->group(function () {

        // Directly secure the routes via an underlying verification callback mapping
        $adminRoutes = function () {
            Route::get('/manage-users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
            Route::get('/manage-users/{user}/impersonate', [\App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('users.impersonate');
            Route::post('/manage-users/{user}/upgrade', [\App\Http\Controllers\Admin\UserController::class, 'upgradeToPro'])->name('users.upgrade');
        };

        // We bind the admin check condition cleanly here by handling it inside the execution block or via a clean gate.
        // Let's use the native Laravel gate workaround to completely avoid Closures in the middleware array:
        Route::middleware(['can:admin'])->group($adminRoutes);
    });
});

require __DIR__.'/auth.php';

Route::post(
    '/stripe/webhook',
    [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']
);