<?php

namespace App\Http\Controllers;

use App\Mail\TradeCompletedNotification;
use App\Models\Purchase;
use App\Models\TradeRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TradeRatingController extends Controller
{
    public function store(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
        ], [
            'stars.required' => '評価を選択してください',
            'stars.min' => '評価は1〜5で選択してください',
            'stars.max' => '評価は1〜5で選択してください',
        ]);

        $currentUser = auth()->user();

        $isBuyer = $purchase->user_id === $currentUser->id;
        $isSeller = $purchase->item->user_id === $currentUser->id;

        if (! $isBuyer && ! $isSeller) {
            abort(403, 'この取引に関係していません');
        }

        $existingRating = TradeRating::where('purchase_id', $purchase->id)
            ->where('rater_id', $currentUser->id)
            ->first();

        if ($existingRating) {
            return redirect('/')->with('error', '既に評価済みです');
        }

        if ($isBuyer) {
            $ratee = $purchase->item->user;

            TradeRating::create([
                'purchase_id' => $purchase->id,
                'rater_id' => $currentUser->id,
                'ratee_id' => $ratee->id,
                'stars' => $validated['stars'],
            ]);

            $purchase->update([
                'status' => Purchase::STATUS_AWAITING_SELLER,
            ]);

            Mail::to($ratee->email)->send(new TradeCompletedNotification($purchase, $currentUser));

        } else {
            $ratee = $purchase->user;

            TradeRating::create([
                'purchase_id' => $purchase->id,
                'rater_id' => $currentUser->id,
                'ratee_id' => $ratee->id,
                'stars' => $validated['stars'],
            ]);

            $purchase->update([
                'status' => Purchase::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            $purchase->item->update([
                'is_sold' => true,
            ]);
        }

        return redirect('/')->with('success', '評価を送信しました');
    }
}
