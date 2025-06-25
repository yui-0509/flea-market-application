<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(){
        $items = Item::all();
        return view('index', compact('items'));
    }

    public function search(Request $request){
        $keyword = $request->input('keyword');

        $items = Item::where('item_name', 'like', "%{$keyword}%")->get();

        return view('index', compact('items'));
    }

    public function create(){
        return view('products.sell');
    }
}
