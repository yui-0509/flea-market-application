<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Models\Profile;

class ProfileController extends Controller
{
    public function create(){
        $user = auth()->user();
        return view('profile.create', compact('user'));
    }

    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest){

        $user = auth()->user();

        $user->username = $addressRequest->input('username');
        $user->save();

        $profileData = $profileRequest->validated();
        if ($profileRequest->hasFile('profile_image')) {
            $imagePath = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $profileData['profile_image'] = $imagePath;
        }

        $addressData = $addressRequest->validated();

        $profile = $user->profile()->firstOrNew([]);
        $profile->fill(array_merge($addressData, $profileData));
        $profile->user_id = $user->id;
        $profile->save();

        return redirect('/');
    }

    public function show(){
        $user = auth()->user();
        $profile = $user->profile;

        $tab = request('tab');

        if ($tab === 'buy') {
            // 購入した商品（購入履歴）
            $items = $user->purchases()->with('item')->get()->pluck ('item');
        } else {
            // デフォルト：出品した商品
            $items = $user->items; // itemsリレーション（1対多）
        }

        return view('profile.show', compact('user', 'profile', 'items', 'tab'));
    }

    public function edit() {
        $user = auth()->user();
        $profile = $user->profile;

        return view('profile.edit', compact('user', 'profile'));
    }
}
