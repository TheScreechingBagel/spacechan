<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $emailRules = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class)->ignore($this->user()->id),
        ];

        if (config('auth.allowed_email_domain')) {
            $emailRules[] = 'ends_with:@'.config('auth.allowed_email_domain');
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
        ];
    }
}
