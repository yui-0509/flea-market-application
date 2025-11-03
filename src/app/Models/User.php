<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function tradeRoomParticipants()
    {
        return $this->hasMany(TradeRoomParticipant::class);
    }

    public function tradeMessages()
    {
        return $this->hasMany(TradeMessage::class, 'sender_id');
    }

    public function givenRatings()
    {
        return $this->hasMany(TradeRating::class, 'rater_id');
    }

    public function receivedRatings()
    {
        return $this->hasMany(TradeRating::class, 'ratee_id');
    }

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function averageRating()
    {
        $average = $this->receivedRatings()->avg('stars');

        if ($average === null) {
            return null; // 評価なし
        }

        return round($average); // 四捨五入
    }

    public function hasRatings()
    {
        return $this->receivedRatings()->count() > 0;
    }
}
