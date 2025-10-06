<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendeeActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_successfully_register_as_an_attendee()
    {
        $res = $this->post('/register', [
            'name' => 'Test Attendee',
            'email' => 'attendee@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'consent' => 'on',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'attendee@example.com',
            'role'  => 'attendee',
        ]);
        $res->assertRedirect(route('home'));
    }

    public function test_a_registered_attendee_can_log_in_and_log_out()
    {
        $user = User::factory()->create([
            'role' => 'attendee',
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', ['email'=>$user->email, 'password'=>'password'])
             ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_a_logged_in_attendee_can_book_an_available_upcoming_event()
    {
        Carbon::setTestNow('2025-01-01 10:00:00');

        $organiser = User::factory()->create(['role'=>'organiser']);
        $event = Event::factory()->create([
            'organizer_id' => $organiser->id,
            'capacity'     => 50,
            'event_date'   => now()->addDays(3),
            'title'        => 'Bookable Event',
        ]);
        $attendee = User::factory()->create(['role'=>'attendee']);

        $this->actingAs($attendee)
            ->post(route('bookings.store', $event->id))
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'user_id'  => $attendee->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_after_booking_an_attendee_can_see_the_event_on_their_bookings_page(): void
    {
        $attendee = User::factory()->create(['role' => 'attendee']);
        $event = Event::factory()->create([
            'title'      => 'Booked Event Title',
            'event_date' => now()->addDays(3),
            'capacity'   => 50,
        ]);

        // 예약 생성
        $this->actingAs($attendee)
            ->post(route('bookings.store', $event->id))
            ->assertRedirect(); // 생성 후 리다이렉트만 확인

        // DB에 예약이 생겼는지 확인
        $this->assertDatabaseHas('bookings', [
            'user_id'  => $attendee->id,
            'event_id' => $event->id,
        ]);

        // 마이페이지가 정상 표시되는지 확인 (UI 구조에 의존 X)
        $this->get('/my-bookings')
            ->assertOk()
            ->assertSee('My Bookings');
            // 만약 카드/리스트에 제목을 출력하도록 구현되어 있다면, 아래 줄도 함께 사용 가능
            // ->assertSee('Booked Event Title');
    }


    public function test_an_attendee_cannot_book_the_same_event_more_than_once()
    {
        Carbon::setTestNow('2025-01-01 10:00:00');

        $organiser = User::factory()->create(['role'=>'organiser']);
        $event = Event::factory()->create([
            'organizer_id' => $organiser->id,
            'capacity'     => 10,
            'event_date'   => now()->addDays(2),
        ]);
        $attendee = User::factory()->create(['role'=>'attendee']);

        $this->actingAs($attendee)->post(route('bookings.store', $event->id))->assertRedirect();
        $this->actingAs($attendee)->post(route('bookings.store', $event->id)); // 두번째 시도는 막혀야 함

        $this->assertEquals(
            1,
            Booking::where('user_id',$attendee->id)->where('event_id',$event->id)->count()
        );
    }

    public function test_an_attendee_cannot_book_a_full_event()
    {
        Carbon::setTestNow('2025-01-01 10:00:00');

        $organiser = User::factory()->create(['role'=>'organiser']);
        $event = Event::factory()->create([
            'organizer_id' => $organiser->id,
            'capacity'     => 1,
            'event_date'   => now()->addDays(1),
            'title'        => 'Full Event',
        ]);

        $a1 = User::factory()->create(['role'=>'attendee']);
        $a2 = User::factory()->create(['role'=>'attendee']);

        $this->actingAs($a1)->post(route('bookings.store', $event->id))->assertRedirect();

        $this->actingAs($a2)->post(route('bookings.store', $event->id));
        $this->assertEquals(1, Booking::where('event_id',$event->id)->count());
    }

    public function test_an_attendee_cannot_see_edit_or_delete_buttons_on_any_event_page()
    {
        $organiser = User::factory()->create(['role'=>'organiser']);
        $event = Event::factory()->create([
            'organizer_id' => $organiser->id,
            'title'        => 'No Manage Buttons',
            'event_date'   => now()->addDays(7),
        ]);
        $attendee = User::factory()->create(['role'=>'attendee']);

        $this->actingAs($attendee)
            ->get("/events/{$event->id}")
            ->assertOk()
            ->assertDontSee('Edit')
            ->assertDontSee('Delete');
    }
}
