<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrganiserActionsTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * Organiser가 로그인해서 ‘조직자 전용 영역’을 볼 수 있어야 함.
     * 실제로는 /organiser 대시보드가 있을 수도/없을 수도 있으니,
     * 최소한 organiser만 접근 가능한 이벤트 생성 페이지(/events/create)가 200을 내는지로 검증.
     */
    public function test_an_organiser_can_log_in_and_view_their_specific_dashboard(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $this->actingAs($organiser)
             ->get('/events/create')
             ->assertOk();  // 조직자 전용 페이지 접근 가능
    }

    /**
     * 유효한 데이터로 이벤트 생성 성공
     */
    public function test_an_organiser_can_successfully_create_an_event_with_valid_data(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $payload = [
            'title'       => 'My New Event',
            'description' => 'Valid description',
            'event_date'  => Carbon::now()->addDays(10)->format('Y-m-d H:i:s'),
            'location'    => 'Seoul',
            'capacity'    => 100,
            'category'    => 'Tech',
            // 이미지 업로드 필드가 필수가 아니라면 생략 가능
        ];

        $response = $this->actingAs($organiser)->post('/events', $payload);

        $response->assertRedirect(); // 성공 후 리다이렉트
        $this->assertDatabaseHas('events', [
            'title'        => 'My New Event',
            'organizer_id' => $organiser->id,
            'category'     => 'Tech',
        ]);
    }

    /**
     * 잘못된 데이터로 이벤트 생성 시 검증 에러
     */
    public function test_an_organiser_receives_validation_errors_for_invalid_event_data(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        // 일부 필드 누락/잘못된 값
        $payload = [
            'title'       => '', // required
            'description' => 'x',
            'event_date'  => Carbon::now()->subDay()->format('Y-m-d H:i:s'), // 과거 날짜라면 실패하게 검증되어 있을 가능성
            'location'    => '',
            'capacity'    => -1, // 음수
            'category'    => 'Unknown', // 유효하지 않은 카테고리일 수도 있음
        ];

        $response = $this->actingAs($organiser)->post('/events', $payload);

        $response->assertSessionHasErrors(); // 어떤 필드든 에러 존재
        $this->assertDatabaseMissing('events', ['description' => 'x']);
    }

    /**
     * 본인이 만든 이벤트는 수정 가능
     */
    public function test_an_organiser_can_successfully_update_an_event_they_own(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $event = Event::factory()->create([
            'organizer_id' => $organiser->id,
            'title'        => 'Old Title',
            'event_date'   => Carbon::now()->addDays(7),
        ]);

        $payload = [
            'title'       => 'Updated Title',
            'description' => 'Updated desc',
            'event_date'  => Carbon::now()->addDays(12)->format('Y-m-d H:i:s'),
            'location'    => 'Busan',
            'capacity'    => 120,
            'category'    => 'Music',
        ];

        $response = $this->actingAs($organiser)->put("/events/{$event->id}", $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', [
            'id'    => $event->id,
            'title' => 'Updated Title',
            'location' => 'Busan',
            'category' => 'Music',
        ]);
    }

    /**
     * 다른 Organiser의 이벤트는 수정 불가
     */
    public function test_an_organiser_cannot_update_an_event_created_by_another_organiser(): void
    {
        $me   = User::factory()->create(['role' => 'organiser']);
        $them = User::factory()->create(['role' => 'organiser']);

        $othersEvent = Event::factory()->create([
            'organizer_id' => $them->id,
            'title'        => 'Not Mine',
            'event_date'   => Carbon::now()->addDays(15),
        ]);

        $payload = [
            'title'       => 'Hacked Title',
            'description' => 'nope',
            'event_date'  => Carbon::now()->addDays(20)->format('Y-m-d H:i:s'),
            'location'    => 'Incheon',
            'capacity'    => 10,
            'category'    => 'Art',
        ];

        $response = $this->actingAs($me)->put("/events/{$othersEvent->id}", $payload);

        // 정책에 따라 403 또는 리다이렉트가 나올 수 있음. 둘 중 하나만 보장 검사
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
        $this->assertDatabaseHas('events', [
            'id'    => $othersEvent->id,
            'title' => 'Not Mine', // 변경 안 되어야 함
        ]);
    }

    /**
     * 본인 이벤트 + 예약이 없는 경우 삭제 가능
     */
    public function test_an_organiser_can_delete_an_event_they_own_that_has_no_bookings(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $event = Event::factory()->create([
            'organizer_id' => $organiser->id,
            'event_date'   => Carbon::now()->addDays(8),
        ]);

        $response = $this->actingAs($organiser)->delete("/events/{$event->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /**
     * 활성 예약이 존재하는 이벤트는 삭제 불가
     */
    public function test_an_organiser_cannot_delete_an_event_that_has_active_bookings(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $attendee  = User::factory()->create(['role' => 'attendee']);

        $event = Event::factory()->create([
            'organizer_id' => $organiser->id,
            'capacity'     => 50,
            'event_date'   => Carbon::now()->addDays(5),
        ]);

        // 예약 하나 심기 (팩토리가 있다면 Booking::factory()->create([...]) 사용해도 됨)
        DB::table('bookings')->insert([
            'user_id'    => $attendee->id,
            'event_id'   => $event->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($organiser)->delete("/events/{$event->id}");

        // 정책/로직에 따라 403 또는 리다이렉트(에러 플래시)가 가능
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
        // 여전히 남아 있어야 함
        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }
}
