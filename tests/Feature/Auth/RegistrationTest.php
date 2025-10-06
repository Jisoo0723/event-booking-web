<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'consent' => 'on',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
        $response->assertSessionHasNoErrors();

    }

    public function test_user_cannot_register_without_agreeing_to_privacy_policy(): void
    {
        // 동의 없이 제출
        $response = $this->from('/register')->post('/register', [
            'name' => 'No Consent',
            'email' => 'no-consent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // 'consent' 생략 (의도적으로 동의 안 함)
        ]);

        // 게스트 상태 유지 + 등록 페이지로 리다이렉트 + 에러 메시지
        $this->assertGuest();
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['consent']);

        // 사용자 생성되지 않음
        $this->assertDatabaseMissing('users', ['email' => 'no-consent@example.com']);
    }

}
