<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(){
        return view('profile.edit');
    }

    public function show(){
    $user = auth()->user();
    $profile = $user->profile;
    return view('profile.show', compact('user', 'profile'));
    }
}
