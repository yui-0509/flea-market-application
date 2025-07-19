<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;

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
        return view('profile.show', compact('user', 'profile'));
    }
}
