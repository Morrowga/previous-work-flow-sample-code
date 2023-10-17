<?php

namespace Src\BlendedConcept\ClassRoom\Presentation\HTTP;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\StoryBookVersionEloquentModel;

class ConductLessonController
{
    public function index()
    {
        return Inertia::render(config('route.conduct_lessons.index'));
    }

    public function show(Request $request)
    {
        $version_id = $request->query('storybook_id');
        $version = StoryBookVersionEloquentModel::findOrFail($version_id);
        $h5p_contents = DB::table('h5p_contents')->where('id', $version->h5p_id)->first();
        $searchString = 'videos/';
        $youtube = 'youtube';
        $path = json_decode($h5p_contents->parameters, true)['interactiveVideo']['video']['files'][0]['path'];
        $video = '';

        if (strpos($path, $searchString) !== false) {
            $video = env('APP_URL') . '/storage/h5p/content/' . $version->h5p_id . '/' . $path;
        } else {
            if (strpos($path, $youtube) !== false) {
                $video = preg_replace('/watch\?v=/', 'embed/', $path);
            } else {
                $video = str_replace('vimeo.com', 'player.vimeo.com/video', $path);
            }
            // $video = $path;
        }

        return Inertia::render(config('route.conduct_lessons.show'), [
            "version" => $version,
            "video" => $video
        ]);
    }
}
