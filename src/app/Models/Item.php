<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_name',
        'brand_id',
        'price',
        'description',
        'status',
        'item_image',
        'is_sold'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'item_category', 'item_id', 'category_id');
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

    public function isLikedBy($user){
        if (!$user) return false;
        return $this->likes->contains('user_id', $user->id);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

    public const STATUS_LIST = [
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    ];
}