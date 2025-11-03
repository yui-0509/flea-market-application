<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Purchase;

class ProfileController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        return view('profile.create', compact('user'));
    }

    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
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

    public function show()
    {
        $user = auth()->user();
        $profile = $user->profile;

        $averageRating = $user->averageRating();
        $hasRatings = $user->hasRatings();

        $tab = request('tab', 'sell');

        $buyingPurchases = $user->purchases()
            ->where('status', Purchase::STATUS_TRADING)
            ->with(['room.messages', 'room.participants' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        $sellingPurchases = Purchase::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereIn('status', [Purchase::STATUS_TRADING, Purchase::STATUS_AWAITING_SELLER])
            ->with(['room.messages', 'room.participants' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        $allPurchases = $buyingPurchases->merge($sellingPurchases);

        $totalUnreadCount = $allPurchases->map(function ($purchase) use ($user) {
            $participant = $purchase->room?->participants->first();

            if (! $participant) {
                return 0;
            }

            return $purchase->room->messages
                ->where('sender_id', '!=', $user->id)
                ->filter(function ($message) use ($participant) {
                    return $message->created_at > ($participant->last_read_at ?? $message->created_at);
                })
                ->count();
        })->sum();

        if ($tab === 'buy') {
            $items = $user->purchases()->with('item')->get()->pluck('item');
        } elseif ($tab === 'trading') {
            $buyingPurchases = $user->purchases()
                ->where('status', Purchase::STATUS_TRADING)
                ->with(['item', 'room.messages', 'room.participants' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->get();

            $sellingPurchases = Purchase::whereHas('item', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->whereIn('status', [Purchase::STATUS_TRADING, Purchase::STATUS_AWAITING_SELLER])
                ->with(['item', 'room.messages', 'room.participants' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->get();

            $allPurchases = $buyingPurchases->merge($sellingPurchases);

            $items = $allPurchases->map(function ($purchase) use ($user) {
                $item = $purchase->item;
                $participant = $purchase->room?->participants->first();

                if ($participant) {
                    $unreadCount = $purchase->room->messages
                        ->where('sender_id', '!=', $user->id)
                        ->filter(function ($message) use ($participant) {
                            return $message->created_at > ($participant->last_read_at ?? $message->created_at);
                        })
                        ->count();
                } else {
                    $unreadCount = 0;
                }

                $item->unread_count = $unreadCount;
                $item->purchase = $purchase;

                return $item;
            })->sortByDesc(function ($item) {
                if ($item->purchase->room && $item->purchase->room->messages->isNotEmpty()) {
                    return $item->purchase->room->messages->first()->created_at;
                }

                return $item->purchase->created_at;
            });
        } else {
            $items = $user->items;
        }

        return view('profile.show', compact('user', 'profile', 'items', 'tab', 'totalUnreadCount', 'averageRating', 'hasRatings'));
    }

    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('profile.edit', compact('user', 'profile'));
    }
}
