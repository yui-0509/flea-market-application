<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'payment',
        'shipping_post_code',
        'shipping_address',
        'shipping_building',
    ];

    const PAYMENT_METHODS = [
        'convenience' => 'コンビニ払い',
        'card' => 'カード払い',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getPaymentLabel($key)
    {
        return self::PAYMENT_METHODS[$key] ?? '未設定';
    }
}
