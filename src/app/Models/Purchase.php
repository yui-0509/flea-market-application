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
        'status',
        'completed_at',
    ];

    const PAYMENT_METHODS = [
        'convenience' => 'コンビニ払い',
        'card' => 'カード払い',
    ];

    const STATUS_TRADING = 'trading';

    const STATUS_AWAITING_SELLER = 'awaiting_seller_rating';

    const STATUS_COMPLETED = 'completed';

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->hasOne(TradeRoom::class);
    }

    public function ratings()
    {
        return $this->hasMany(TradeRating::class);
    }

    public function scopeTrading($query)
    {
        return $query->where('status', self::STATUS_TRADING);
    }

    public function scopeAwaitingSeller($query)
    {
        return $query->where('status', self::STATUS_AWAITING_SELLER);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public static function getPaymentLabel($key)
    {
        return self::PAYMENT_METHODS[$key] ?? '未設定';
    }

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}
