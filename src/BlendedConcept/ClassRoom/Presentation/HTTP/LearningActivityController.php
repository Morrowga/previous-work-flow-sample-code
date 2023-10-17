<?php

namespace Src\BlendedConcept\ClassRoom\Presentation\HTTP;

use Inertia\Inertia;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\StoryBookEloquentModel;

class LearningActivityController
{
    public function index(StoryBookEloquentModel $storybook)
    {
        return Inertia::render(config('route.learning_activities.index'), [
            "storybook" => $storybook->load(['devices', 'learningneeds', 'themes', 'disability_types', 'storybook_versions'])
        ]);
    }
}
