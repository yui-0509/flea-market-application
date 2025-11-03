<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeRating extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'rater_id', 'ratee_id', 'stars'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratee()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }

    const MIN_STARS = 1;

    const MAX_STARS = 5;
}
