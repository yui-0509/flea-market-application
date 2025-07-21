<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;

Route::post('/register', function (RegisterRequest $request) {
        $creator = app(CreateNewUser::class);
        $user = $creator->create($request->all());

        Auth::login($user);

        return redirect('/mypage/profile/create');
    });
Route::get('/search', [ItemController::class, 'search']);
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('products.detail');

Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
    Route::get('/mypage/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::patch('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'show']);
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell/store', [ItemController::class, 'store']);
    Route::post('/item/like/{item}', [ItemController::class, 'addlike']);
    Route::delete('/item/like/{item}', [ItemController::class, 'destroy']);
    Route::post('/item/comment/{item}', [ItemController::class, 'comment'])->name('item.comment');
    Route::get('/purchase/{item}', [PurchaseController::class, 'purchase'])->name('purchase');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'address'])->name('purchase.address');
    Route::patch('/purchase/address/update/{item}', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::post('/purchase/store/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
});