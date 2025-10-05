<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
    ];

    // 예약은 하나의 이벤트에 속함
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // 예약은 하나의 사용자(참가자)에 속함
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
