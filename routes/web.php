<?php

use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Http\Controllers\ResourceController;
use Cone\Root\Http\Middleware\Authorize;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Resource
Route::middleware([Authorize::class.':_resource'])->group(static function (): void {
    Route::get('/{resource}', [ResourceController::class, 'index'])->name('resource.index');
    Route::get('/{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
    Route::post('/{resource}', [ResourceController::class, 'store'])->name('resource.store');
    Route::get('/{resource}/{resourceModel}', [ResourceController::class, 'show'])->name('resource.show');
    Route::get('/{resource}/{resourceModel}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
    Route::patch('/{resource}/{resourceModel}', [ResourceController::class, 'update'])->name('resource.update');
    Route::delete('/{resource}/{resourceModel}', [ResourceController::class, 'destroy'])->name('resource.delete');
    Route::post('/{resource}/{resourceModel}/restore', [ResourceController::class, 'restore'])->name('resource.restore');
});
