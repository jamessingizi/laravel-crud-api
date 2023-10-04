<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GetUserRequest extends FormRequest
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
            'id' => 'required|integer'
        ];
    }

    public function getValidatorInstance(): Validator
    {
        $data = $this->all();
        $data['id'] = $this->route('id');
        $this->getInputSource()->replace($data);

        return parent::getValidatorInstance();
    }
}
