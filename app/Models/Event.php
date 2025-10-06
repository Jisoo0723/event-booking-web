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
        'title','description','event_date','location','capacity',
        'organiser_id','organizer_id', 
        'category','image',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function organiser()
    {
        return $this->belongsTo(User::class, 'organizer_id'); 
    }

    // organiser_id <-> organizer_id 자동 브리지
    public function setOrganiserIdAttribute($value)
    {
        // 코드에서 organiser_id에 값을 넣으면 실제로는 organizer_id에 저장됨
        $this->attributes['organizer_id'] = $value;
    }

    public function getOrganiserIdAttribute()
    {
        // organiser_id로 읽으면 organizer_id 값을 돌려줌
        return $this->attributes['organizer_id'] ?? null;
    }

    public function organizer()
    {
        return $this->organiser();
    }

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
        // status 컬럼이 있고 'cancelled'를 제외한다고 가정
        $active = $this->bookings()->where('status', '!=', 'cancelled')->count();
        return max(0, (int)$this->capacity - $active);
    }

    public function isPast(): bool
    {
        // 현재 시각보다 이전이면 과거로 간주
        return $this->event_date?->lt(now(config('app.timezone'))) ?? false;
    }

    // 앞으로 있을 이벤트만 (정렬은 컨트롤러에서)
    public function scopeUpcoming(Builder $q): Builder
    {
        // 앞으로 있을 이벤트만 (정렬은 컨트롤러에서)
        return $q->where('event_date', '>', now(config('app.timezone')));
    }

    // (옵션) 지난 이벤트
    public function scopePast(Builder $query): Builder
    {
        // 지난 이벤트
        return $query->where('event_date', '<=', now(config('app.timezone')));
    }

    public function scopeCategory($query, $category)
    {
        // 파라미터 없거나 'All'이면 필터 안함
        if (!$category || $category === 'All') {
            return $query;
        }
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $q)
    {
        if (!$q) return $query;

        return $query->where(function ($qq) use ($q) {
            $qq->where('title', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->orWhere('location', 'like', "%{$q}%");
        });
    }

}
