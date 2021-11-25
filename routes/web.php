<?php

use Cone\Root\Http\Controllers\DashboardController;
use Cone\Root\Http\Controllers\ExtractController;
use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Media
Route::apiResource('media', MediaController::class)->only(['store', 'destroy']);

// Extracts
Route::get('/{resource}/extracts/{extract}', [ExtractController::class, 'index'])->name('resource.extract.index');
Route::post('/{resource}/extracts/{extract}', [ExtractController::class, 'action'])->name('resource.extract.action');

// Resource
Route::get('/{resource}', [ResourceController::class, 'index'])->name('resource.index');
Route::get('/{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
Route::post('/{resource}', [ResourceController::class, 'store'])->name('resource.store');
Route::post('/{resource}/action', [ResourceController::class, 'action'])->name('resource.action');
Route::get('/{resource}/{id}', [ResourceController::class, 'show'])->name('resource.show');
Route::get('/{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
Route::patch('/{resource}/{id}', [ResourceController::class, 'update'])->name('resource.update');
Route::delete('/{resource}/{id}', [ResourceController::class, 'destroy'])->name('resource.destroy');
