<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function detail(Item $item){
    return view('products.detail', compact('item'));
    }

    public function create(){
        $categories = Category::all();
        $statuses = Item::STATUS_LIST;

        return view('products.sell', compact('categories', 'statuses'));
    }

    public function store(ExhibitionRequest $request){
        $item = new Item();

        $path = null;
        $imageUrl = null;
        if ($request->hasFile('item_image')) {
            $path = $request->file('item_image')->store('item_images', 's3');
            $imageUrl = Storage::disk('s3')->url($path);
            $item->item_image = $imageUrl;
        }

        $item->user_id = Auth::id();
        $item->item_name = $request->item_name;

        $brandName = $request->input('brand_name');
        $brand = Brand::firstOrCreate(['brand_name' => $brandName]);
        $item->brand_id = $brand->id;

        $item->description = $request->description;
        $item->status = $request->status;
        $item->price = $request->price;

        $item->save();

        $item->categories()->attach($request->categories);

        return redirect('/');
    }
}
