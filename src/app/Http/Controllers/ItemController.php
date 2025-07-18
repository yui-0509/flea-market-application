<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(){

        if (request('tab') === 'mylist') {
            // ログインユーザーがいいねした商品のみ取得
            $items = auth()->user()
                        ->likes()
                        ->with('item')
                        ->get()
                        ->pluck('item'); // itemだけ取り出す
        } else {
            // 通常のおすすめ一覧（仮に新着順）
            $items = Item::orderBy('created_at', 'desc')->get();
        }
        return view('index', compact('items'));
    }

    public function search(Request $request){
        $keyword = $request->input('keyword');

        $items = Item::where('item_name', 'like', "%{$keyword}%")->get();

        return view('index', compact('items'));
    }

    public function detail(Item $item){
        $statusText = Item::STATUS_LIST[$item->status];

        return view('products.detail', compact('item', 'statusText'));
    }

    public function create(){
        $categories = Category::all();
        $statuses = Item::STATUS_LIST;

        return view('products.sell', compact('categories', 'statuses'));
    }

    public function store(ExhibitionRequest $request){
        $validated = $request->validated();

        $item = new Item();

        if ($request->hasFile('item_image')) {
        $path = $request->file('item_image')->store('item_images', 'public');
        $item->item_image = 'storage/' . $path;
        }

        $item->user_id = Auth::id();
        $item->item_name = $validated['item_name'];
        $item->description = $validated['description'];
        $item->status = $validated['status'];
        $item->price = $validated['price'];

        $brandName = $request->input('brand_name');

        if (!empty($brandName)) {
            $brand = Brand::firstOrCreate(['brand_name' => $brandName]);
            $item->brand_id = $brand->id;
        }


        $item->save();
        $item->categories()->attach($validated['categories']);

        return redirect('/');
    }

    public function addlike(Item $item){

        // すでにいいねしてるかチェック（重複防止）
        if (!$item->likes()->where('user_id', auth()->id())->exists()) {
            $item->likes()->create([
            'user_id' => auth()->id(),]);
        }

        return response()->json([
            'message' => 'liked',
            'count' => $item->likes()->count()]);
    }

    public function destroy(Item $item){

        $item->likes()->where('user_id', auth()->id())->delete();

        return response()->json([
            'message' => 'unliked',
            'count' => $item->likes()->count()
        ]);
    }

    public function comment(CommentRequest $request, Item $item){

        $validated = $request->validated();

        $comment = Comment::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        $comment->load('user.profile');

        return response()->json([
            'success' => true,
            'comment_count' => $item->comments()->count(),
            'comment' => [
            'author' => $comment->user->name,
            'content' => nl2br(e($comment->content)),
            'avatar' => $comment->user->profile && $comment->user->profile->profile_image
            ? asset($comment->user->profile->profile_image)
            : null,
            ],
        ]);
    }
}
