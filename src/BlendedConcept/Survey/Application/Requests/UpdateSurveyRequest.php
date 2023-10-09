<?php

namespace Src\BlendedConcept\Survey\Application\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Src\BlendedConcept\Survey\Application\Rules\StartEndDateValidationRule;

class UpdateSurveyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'type' => [
                'required',
                Rule::in(['USEREXP', 'PROFILING']),
            ],
            'user_type' => ['required', 'string'],
            'appear_on' => ['required', 'string'],
            'start_date' => ['required', 'date', new StartEndDateValidationRule],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'required' => ['required', 'boolean'],
            'repeat' => ['required', 'boolean'],
            'questions' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'title' => 'Title is required',
            'description' => 'Description is required',
            'user_type' => 'User Type is required',
            'appear_on' => 'Appear on is required',
            'start_date' => 'Start Date on is required',
            'end_date' => 'End Date on is required',
        ];
    }
}
