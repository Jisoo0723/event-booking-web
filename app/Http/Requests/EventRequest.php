<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        // routes/web.php 에서 이미 can:isOrganiser 미들웨어로 보호됨
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'event_date'  => ['required','date','after:now'], // 과거 금지
            'location'    => ['required','string','max:255'],
            'capacity'    => ['required','integer','min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_date.after' => 'Event date must be in the future.',
        ];
    }
}
