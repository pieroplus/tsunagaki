<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SigninRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
        public function rules(): array
        {
            return [
                'email' => 'nullable|string',
                'name' => 'nullable|string',
                'password' => 'required|string|min:8',
            ];
        }

        public function withValidator($validator)
        {
            $validator->after(function ($validator) {
                if (!$this->email && !$this->name) {
                    $validator->errors()->add('email_or_name', 'emailかnameのいずれかを入力してください。');
                }
            });
        }

}
