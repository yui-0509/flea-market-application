<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;

Route::get('/search', [ItemController::class, 'search']);

Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::get('/mypage', [ProfileController::class, 'show']);
    Route::get('/sell', [ItemController::class, 'create']);
});