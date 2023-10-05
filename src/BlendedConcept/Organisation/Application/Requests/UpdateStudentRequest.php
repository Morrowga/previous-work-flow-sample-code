<?php

namespace Src\BlendedConcept\Organisation\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'gender' => [
                'required',
            ],
            'dob' => [
                'required',
            ],
            'education_level' => [
                'required',
            ],

        ];
    }

    public function messages()
    {
        return [
            'gender.required' => 'Please select a gender',
            'dob' => 'Enter your date of birth',
            'education_level' => 'Enter your education level',
            'contact_number.required' => 'Enter a parent contact number',
            'contact_number.unique' => 'This phone number is already in use',
            'email.required' => 'Enter your email address',
            'email.email' => 'Enter a valid email address',
            'email.unique' => 'This email is already in use',
        ];
    }
}
