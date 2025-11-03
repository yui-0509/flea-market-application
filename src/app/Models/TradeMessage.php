<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['room_id', 'sender_id', 'body', 'image_path', 'edited_at'];

    public function room()
    {
        return $this->belongsTo(TradeRoom::class, 'room_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    protected $casts = [
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
