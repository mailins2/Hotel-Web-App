<?php

use App\Http\Controllers\HotelManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [HotelManagementController::class, 'dashboard'])->name('dashboard');
Route::get('/terms-of-use', [HotelManagementController::class, 'termOfUse'])->name('pages.term-of-use');
Route::post('/logout', function () {
    auth()->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/dashboard');
})->name('logout');

Route::prefix('hotel')->name('hotel.')->group(function () {
    Route::get('/reports', [HotelManagementController::class, 'report'])->name('reports.index');

    foreach (array_keys(config('hotel-management.modules', [])) as $moduleKey) {
        Route::get("/{$moduleKey}", [HotelManagementController::class, 'index'])->defaults('moduleKey', $moduleKey)->name($moduleKey . '.index');
        Route::get("/{$moduleKey}/create", [HotelManagementController::class, 'create'])->defaults('moduleKey', $moduleKey)->name($moduleKey . '.create');
        Route::post("/{$moduleKey}", [HotelManagementController::class, 'store'])->defaults('moduleKey', $moduleKey)->name($moduleKey . '.store');
        Route::get("/{$moduleKey}/{recordId}", [HotelManagementController::class, 'show'])->defaults('moduleKey', $moduleKey)->name($moduleKey . '.show');
        Route::get("/{$moduleKey}/{recordId}/edit", [HotelManagementController::class, 'edit'])->defaults('moduleKey', $moduleKey)->name($moduleKey . '.edit');
        Route::match(['put', 'patch'], "/{$moduleKey}/{recordId}", [HotelManagementController::class, 'update'])->defaults('moduleKey', $moduleKey)->name($moduleKey . '.update');
        Route::delete("/{$moduleKey}/{recordId}", [HotelManagementController::class, 'destroy'])->defaults('moduleKey', $moduleKey)->name($moduleKey . '.destroy');
    }
});
