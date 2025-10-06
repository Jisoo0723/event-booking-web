<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_can_view_the_paginated_list_of_upcoming_events(): void
    {
        Carbon::setTestNow('2025-01-01 10:00:00');

        // 먼저 organiser 유저를 하나 만든다
        $org = User::factory()->create(['role' => 'organiser']);

        // 앞으로 날짜 이벤트 12개 생성 (페이징 확인용)
        Event::factory()->count(12)->create([
            'organizer_id' => $org->id,
            'event_date'   => now()->addDays(1),
        ]);

        $res = $this->get('/events')->assertOk();
        // 첫 페이지에 최소 한 개의 타이틀이 보이는지(팩토리 기본 필드 기준)
        $res->assertSee('Events'); // 목록 페이지의 헤더/텍스트에 맞춰 수정 가능
    }

    public function test_a_guest_can_view_a_specific_event_details_page(): void
    {
        $org = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create([
            'organizer_id' => $org->id,
            'title'        => 'My Public Event',
            'event_date'   => now()->addDays(3),
        ]);

        $this->get("/events/{$event->id}")
            ->assertOk()
            ->assertSee('My Public Event');
    }

    public function test_a_guest_is_redirected_when_accessing_protected_routes(): void
    {
        // 네 프로젝트에 있는 "보호된" 라우트를 사용. Breeze 기본 `/profile`은 반드시 auth 필요
        $this->get('/profile')->assertRedirect('/login');

        // 필요 시 여기에 다른 보호 라우트 추가 가능:
        // $this->get('/events/create')->assertRedirect('/login'); // 라우트가 있다면 주석 해제
    }

    public function test_a_guest_cannot_see_action_buttons_on_event_details_page(): void
    {
        $org = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create([
            'organizer_id' => $org->id,
            'title'        => 'Hidden Buttons Event',
            'event_date'   => now()->addDays(5),
        ]);

        $this->get("/events/{$event->id}")
            ->assertOk()
            // 게스트는 관리 버튼 없음
            ->assertDontSee('Edit')
            ->assertDontSee('Delete')
            ->assertDontSee('Manage')
            // 게스트에겐 로그인 유도 버튼이 보여야 함
            ->assertSee('Login to Book')
            // 실제 예약 수행 엔드포인트 표시(폼/링크)는 없어야 함 (있으면 주석 해제)
            // ->assertDontSee('/bookings')
            ;
    }
}
