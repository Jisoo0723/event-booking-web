<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;      
use Illuminate\Support\Carbon;                 

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','description','event_date','location','capacity','organizer_id',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function organiser() { return $this->organizer(); }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'bookings')->withTimestamps();
    }

    public function remainingSpots(): int
    {
        return max(0, (int)$this->capacity - (int)$this->bookings()->count());
    }

    public function isPast(): bool
    {
        return optional($this->event_date)->isPast();
    }

    // 앞으로 있을 이벤트만 (정렬은 컨트롤러에서)
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('event_date', '>', Carbon::now());
    }

    // (옵션) 지난 이벤트
    public function scopePast(Builder $query): Builder
    {
        return $query->where('event_date', '<=', Carbon::now());
    }
}
