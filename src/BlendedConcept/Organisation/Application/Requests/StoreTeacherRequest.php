<?php

namespace Src\BlendedConcept\Organisation\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => [
                'required',
            ],
            'last_name' => [
                'required',
            ],
            'email' => ['required', 'email', 'unique:users,email'],
            'contact_number' => [
                'required',
            ],
            // 'image' => [
            //     'required',
            // ],
        ];
    }

    public function messages()
    {
        return [
            'first_name' => 'First Name is required',
            'last_name' => 'Last Name is required',
            'email.required' => 'Enter your email address',
            'email.email' => 'Enter a valid email address',
            'email.unique' => 'This email is already in use',
            'contact_number' => 'Contact Number is required',
        ];
    }
}
