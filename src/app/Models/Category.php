<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name'];

    public function items(){
    return $this->belongsToMany(Item::class, 'item_category', 'category_id', 'item_id');
    }
}
