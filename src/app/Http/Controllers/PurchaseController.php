<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function purchase(Item $item){
        $profile = Auth::user()->profile;
        return view('products.purchase', compact('item', 'profile'));
    }

    public function address($item_id){
        $item = Item::findOrFail($item_id);
        $profile = auth()->user()->profile;

        return view('address', compact('item', 'profile'));
    }

    public function update(Request $request, Item $item){

        $postCode = $request->input('shipping_post_code');
        $address = $request->input('shipping_address');
        $building = $request->input('shipping_building');

        return redirect()
            ->route('purchase', ['item' => $item->id])
            ->with([
                'shipping_post_code' => $postCode,
                'shipping_address' => $address,
                'shipping_building' => $building,
            ]);
    }

    public function store(PurchaseRequest $request, Item $item){
        if ($item->is_sold) {
        return redirect('/');
    }

        $validated = $request->validated();

        Purchase::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'payment' => $validated['payment'],
            'shipping_post_code' => $validated['shipping_post_code'],
            'shipping_address' => $validated['shipping_address'],
            'shipping_building' => $request->input('shipping_building', ''),
        ]);

        $item->is_sold = true;
        $item->save();

        return redirect('/');
    }
}
