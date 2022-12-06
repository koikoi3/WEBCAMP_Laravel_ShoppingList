<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\UserRequest;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User as UserController;
use App\Http\Requests\UserRegisterPost;

class UserRegisterPost extends FormRequest

{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:128'],
            'email' => ['required', 'max:254'],
            'password' => ['required', 'max:72', 'confirmed'],
            'password_confirmation' => ['required', 'max:72'],
        ];
    }
    public function messages()
    {
        return [
            'password.confirmed' => 'パスワードが異なります',
        ];
    }
}
