<?php

namespace Src\BlendedConcept\Organization\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'name' => [
                'string',
                'required',
            ],
            'contact_email' => [
                'required',
                'email',
                'unique:organizations,contact_email,'.request()->route('organization')->id,
            ],
            'org_admin_name' => [
                'required',
            ],
            'org_admin_contact_number' => [

                'required',
            ],
            'login_email' => [
                'required',
                'email',
                'unique:users,email,'.request()->route('organization')->org_admin_id,
            ],
            'login_password' => [
                'required', 'min:8',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name' => 'This organization name already exist',
        ];
    }
}
