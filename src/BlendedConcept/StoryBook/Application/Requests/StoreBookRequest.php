<?php

namespace Src\BlendedConcept\StoryBook\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (request('type') == 'H5P') {
            return [
                'name' => [
                    'required', 'string', 'unique:storybooks,name',
                ],
                'description' => [
                    'required', 'string',
                ],
                'num_gold_coins' => [
                    'required',
                ],
                'num_silver_coins' => [
                    'required',
                ],
                'tags' => [
                    'required', 'array',
                ],
                'sub_learning_needs' => [
                    'required', 'array',
                ],
                'themes' => [
                    'required', 'array',
                ],
                'disability_type' => [
                    'required', 'array',
                ],
                'devices' => [
                    'required', 'array',
                ],

                'h5p_id' => ['required']

            ];
        }
        return [
            'name' => [
                'required', 'string', 'unique:storybooks,name',
            ],
            'description' => [
                'required', 'string',
            ],
            'num_gold_coins' => [
                'required',
            ],
            'num_silver_coins' => [
                'required',
            ],
            'tags' => [
                'required', 'array',
            ],
            'sub_learning_needs' => [
                'required', 'array',
            ],
            'themes' => [
                'required', 'array',
            ],
            'disability_type' => [
                'required', 'array',
            ],
            'devices' => [
                'required', 'array',
            ],

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.unique' => 'Name must be unique',
            'description' => 'Description is required',
            'tags' => 'Tags is required',
            'sub_learning_needs' => 'Learning Needs is required',
            'themes' => 'Themes  is required',
            'disability_type' => 'Disability Type is required',
            'disability_type' => 'Disability Type is required',
            'devices' => 'Supported Accessibility Devices  is required',
            'h5p_id.required' => 'h5p video is required'
        ];
    }
}
