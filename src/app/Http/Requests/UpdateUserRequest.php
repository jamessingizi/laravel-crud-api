<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string:max:32',
            'email' => 'required|email|' . Rule::unique('users')->ignore($this->id),
            'phone_number' => 'required|numeric|min:10',
            'role' => 'required|string|in:admin,user',
        ];
    }
}
