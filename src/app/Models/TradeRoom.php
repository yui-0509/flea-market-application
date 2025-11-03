<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeRoom extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function messages()
    {
        return $this->hasMany(TradeMessage::class, 'room_id');
    }

    public function participants()
    {
        return $this->hasMany(TradeRoomParticipant::class, 'room_id');
    }
}
