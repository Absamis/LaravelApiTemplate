<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
            "firstname" => ["required", "regex: /^[a-zA-Z]{5,32}$/", "max:32"],
            "lastname" => ["required", "regex: /^[a-zA-Z]{5,32}$/", "max:32"],
            "username" => ["required", "regex: /^[a-zA-Z]+[\w@_]+$/", "max:20"],
            "institution" => ["required", "regex: /^\w{5,}(\s|\w)+$/", "max:200"],
            "gender" => ["required", "regex: /^[a-zA-Z]+$/", "max:10"],
            // "email" => ["required", "email:dns", "unique:users,email"],
            "phone" => ["required", "regex: /^\+[1-9]{1,3}[0-9]{10,13}$/", "max:16"],
            // "password" => ["required", Password::min(8)->letters()->numbers(), "max:32"]
        ];
    }

    public function messages()
    {
        return [
            "phone.regex" => "phone number format should be [Country Code] [Numbers]",
            "max" => ":attribute is too long",
            "username.regex" => ":attribute is invalid. It can contain alphanumeric characters, @ and _",
            "regex" => ":attribute is invalid",
            "unique" => ":attribute already exists"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "code" => "33",
            "message" => "Form validation error",
            "data" => [],
            "errors" => $validator->errors()
        ], 400));
    }
}
