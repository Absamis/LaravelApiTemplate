<?php

namespace App\Http\Requests\Auth;

use App\Traits\Enums\ResponseCodeEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class UserRegistrationRequest extends FormRequest
{
    use ResponseCodeEnum;
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
            "username" => ["required", "regex: /^[a-zA-Z]+[\w@_]+$/", "max:20", "unique:users,username"],
            "email" => ["required", "email:dns", "unique:users,email"],
            "phone" => ["required", "regex: /^\+[1-9]{1,3}[0-9]{10,13}$/", "max:16"],
            "password" => ["required", Password::min(8)->letters()->numbers(), "max:32"],
            "confirm_password" => ["required", "same:password"]
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
            "code" => $this->formError["code"],
            "message" => $this->formError["message"],
            "data" => [],
            "errors" => $validator->errors()
        ], 400));
    }
}
