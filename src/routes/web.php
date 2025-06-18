<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});