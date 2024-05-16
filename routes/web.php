<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/', [ImageController::class, 'fetch']);
Route::post('/uploadImages', [ImageController::class, 'upload'])->name('upload');

// APIs
Route::get('/api/images', [ImageController::class, 'fetchAsJson']);
Route::get('/api/images/{id}', [ImageController::class, 'fetchAsJsonById']);

// ZIP
Route::get('/download-zip/{id}', [ImageController::class, 'downloadAsZip'])->name('download-zip');
