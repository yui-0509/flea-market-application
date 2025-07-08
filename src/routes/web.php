<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;

Route::get('/search', [ItemController::class, 'search']);
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('products.detail');

Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::get('/mypage', [ProfileController::class, 'show']);
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell/store', [ItemController::class, 'store']);
    Route::post('/item/like/{item}', [ItemController::class, 'addlike']);
    Route::delete('/item/like/{item}', [ItemController::class, 'destroy']);
    Route::post('/item/comment/{item}', [ItemController::class, 'comment'])->name('item.comment');
});