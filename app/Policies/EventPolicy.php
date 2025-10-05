<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * organiser가 자신의 이벤트를 관리할 수 있는지 여부
     */
    public function manage(User $user, Event $event): bool
    {
        return $user->role === 'organiser' && $event->organizer_id === $user->id;
    }

    /**
     * 수정 권한
     */
    public function update(User $user, Event $event): bool
    {
        return $this->manage($user, $event);
    }

    /**
     * 삭제 권한
     */
    public function delete(User $user, Event $event): bool
    {
        return $this->manage($user, $event);
    }

    /**
     * 보기 권한
     * - 과거 이벤트는 organiser 본인만 접근 가능
     * - 미래 이벤트는 누구나 볼 수 있음
     */
    public function view(?User $user, Event $event): bool
    {
        if ($event->isPast()) {
            return $user && $this->manage($user, $event);
        }
        return true;
    }
}
