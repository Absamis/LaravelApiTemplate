<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
            "name" => ["required", "max:30"],
            "email" => ["required", "email:rfc,dns", "unique:users,email", "max:150"],
            "password" => ["required", Password::min(8)->letters()->numbers(), "max:32"],
            "confirm_password" => ["required", "same:password"]
        ];
    }
}
