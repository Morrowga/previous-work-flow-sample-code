<?php

namespace Src\BlendedConcept\Survey\Application\Mappers;

use Src\BlendedConcept\Survey\Domain\Model\Entities\Question;
use Src\BlendedConcept\Survey\Infrastructure\EloquentModels\QuestionEloquentModel;

class QuestionMapper
{
    public static function fromRequest(array $request, $question_id = null): Question
    {

        return new Question(
            id: $question_id,
            survey_id: $request['survey_id'],
            question_type: $request['question_type'],
            question: $request['question'],
            order: $request['order'],
        );
    }

    public static function toEloquent(Question $question): QuestionEloquentModel
    {
        $questionEloquent = new QuestionEloquentModel();

        if ($question->id) {
            $questionEloquent = QuestionEloquentModel::query()->findOrFail($question->id);
        }

        $questionEloquent->id = $question->id;
        $questionEloquent->survey_id = $question->survey_id;
        $questionEloquent->question_type = $question->question_type;
        $questionEloquent->question = $question->question;
        $questionEloquent->order = $question->order;

        return $questionEloquent;
    }
}
