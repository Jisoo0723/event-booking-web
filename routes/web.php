<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// 홈 (미래 이벤트 목록은 HomeController에서 처리)
Route::get('/', [EventController::class, 'indexPopular'])->name('home');

Route::get('/home/filter', [EventController::class, 'homeFilter'])->name('home.filter');

// 대시보드(필요 시 유지)
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 이벤트 목록(공개)
Route::get('/events', [EventController::class, 'index'])->name('events.index');

Route::middleware('auth')->group(function () {

    // 프로필
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 예약(참가자)
    Route::post('/events/{event}/book', [BookingController::class, 'store'])
        ->name('bookings.store')->whereNumber('event');

    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])
        ->name('bookings.destroy')->whereNumber('booking');   // 취소 라우트 추가

    Route::get('/my-bookings', [BookingController::class, 'index'])
        ->name('bookings.index');

    // Organiser 전용
    Route::middleware('can:isOrganiser')->group(function () {

        // organiser 대시보드
        Route::get('/organiser/dashboard', [EventController::class, 'organiserDashboard'])
            ->name('organiser.dashboard');

        // 생성/저장
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');

        // 수정/삭제 (모델 바인딩: {event})
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])
            ->name('events.edit')->whereNumber('event');

        Route::put('/events/{event}', [EventController::class, 'update'])
            ->name('events.update')->whereNumber('event');

        Route::delete('/events/{event}', [EventController::class, 'destroy'])
            ->name('events.destroy')->whereNumber('event');

        // organiser가 특정 이벤트 하나의 예약 내역을 보는 페이지
        Route::get('/events/{event}/bookings', [BookingController::class, 'manage'])
            ->name('bookings.manage')->whereNumber('event');

        Route::get('/events/{event}/attendees', [BookingController::class, 'attendeesPartial'])
            ->name('events.attendees')
            ->whereNumber('event');

        // 예약 내역 엑셀/CSV 등으로 내보내기
        Route::get('/events/{event}/bookings/export', [BookingController::class, 'export'])
        ->name('bookings.export')->whereNumber('event');
    });
});

// 마지막에 상세 페이지(충돌 방지). event로 바인딩.
Route::get('/events/{event}', [EventController::class, 'show'])
    ->name('events.show')->whereNumber('event');

Route::view('/privacy-policy', 'legal.privacy')->name('privacy.policy');
Route::view('/terms-of-use', 'legal.terms')->name('terms.of.use');


require __DIR__.'/auth.php';
