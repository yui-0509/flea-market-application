<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\TradeRatingController;
use App\Http\Controllers\TradeRoomController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::get('/', [ItemController::class, 'index']);
Route::get('/search', [ItemController::class, 'search']);
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('products.detail');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    session()->forget('unauthenticated_user');

    return redirect('/mypage/profile/create');
})->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    session()->get('unauthenticated_user')->sendEmailVerificationNotification();
    session()->put('resent', true);

    return back()->with('status', 'verification-link-sent');
})->name('verification.send');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/email/verify/check', function () {
    $user = Auth::user();
    if (! $user) {
        $session = session('unauthenticated_user');
        if ($session instanceof User) {
            $user = User::find($session->id);
        }
    }
    if ($user && $user->hasVerifiedEmail()) {
        if (! Auth::check()) {
            Auth::login($user);
        }

        return redirect()->intended('/');
    }

    return redirect('https://mailtrap.io/');
})->name('verification.check');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/mypage/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::patch('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell/store', [ItemController::class, 'store']);
    Route::post('/item/like/{item}', [ItemController::class, 'addLike']);
    Route::delete('/item/like/{item}', [ItemController::class, 'destroy']);
    Route::post('/item/comment/{item}', [ItemController::class, 'comment'])->name('item.comment');

    Route::get('/purchase/{item}', [PurchaseController::class, 'purchase'])->name('purchase');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'address'])->name('purchase.address');
    Route::patch('/purchase/address/update/{item}', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::post('/purchase/store/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    Route::get('/trade-rooms/{room}', [TradeRoomController::class, 'show'])
        ->name('trade-rooms.show');
    Route::post('/trade-rooms/{room}/messages', [TradeRoomController::class, 'storeMessage'])
        ->name('trade-rooms.messages.store');
    Route::patch('/trade-rooms/{room}/messages/{message}', [TradeRoomController::class, 'updateMessage'])
        ->name('trade-rooms.messages.update');
    Route::delete('/trade-rooms/{room}/messages/{message}', [TradeRoomController::class, 'deleteMessage'])
        ->name('trade-rooms.messages.delete');

    Route::post('/purchases/{purchase}/rating', [TradeRatingController::class, 'store'])
        ->name('purchases.rating.store');
});
