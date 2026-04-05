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
    Route::redirect('/reports', '/dashboard')->name('reports.index');

    foreach (array_keys(config('hotel-management.modules', [])) as $moduleKey) {
        Route::get("/{$moduleKey}", fn (HotelManagementController $controller) => $controller->index($moduleKey))
            ->name($moduleKey . '.index');

        Route::get("/{$moduleKey}/create", fn (HotelManagementController $controller) => $controller->create($moduleKey))
            ->name($moduleKey . '.create');

        Route::post("/{$moduleKey}", fn (\Illuminate\Http\Request $request, HotelManagementController $controller) => $controller->store($request, $moduleKey))
            ->name($moduleKey . '.store');

        Route::get("/{$moduleKey}/{recordId}", fn (HotelManagementController $controller, string $recordId) => $controller->show($moduleKey, $recordId))
            ->name($moduleKey . '.show');

        Route::get("/{$moduleKey}/{recordId}/edit", fn (HotelManagementController $controller, string $recordId) => $controller->edit($moduleKey, $recordId))
            ->name($moduleKey . '.edit');

        Route::match(['put', 'patch'], "/{$moduleKey}/{recordId}", fn (\Illuminate\Http\Request $request, HotelManagementController $controller, string $recordId) => $controller->update($request, $moduleKey, $recordId))
            ->name($moduleKey . '.update');

        Route::delete("/{$moduleKey}/{recordId}", fn (HotelManagementController $controller, string $recordId) => $controller->destroy($moduleKey, $recordId))
            ->name($moduleKey . '.destroy');
    }
});
