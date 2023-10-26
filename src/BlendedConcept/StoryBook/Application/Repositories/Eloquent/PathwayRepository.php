<?php

namespace Src\BlendedConcept\StoryBook\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\BlendedConcept\StoryBook\Application\DTO\PathwayData;
use Src\BlendedConcept\StoryBook\Domain\Model\Entities\Pathway;
use Src\BlendedConcept\StoryBook\Domain\Resources\PathwayResource;
use Src\BlendedConcept\StoryBook\Application\Mappers\PathwayMapper;
use Src\BlendedConcept\StoryBook\Domain\Repositories\PathwayRepositoryInterface;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\PathwayEloquentModel;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\StoryBookEloquentModel;

class PathwayRepository implements PathwayRepositoryInterface
{
    public function getPathways($filters)
    {
        $pathways = PathwayResource::collection(PathwayEloquentModel::filter($filters)->with('storybooks')->orderBy('id', 'desc')->paginate($filters['perPage'] ?? 10));

        return $pathways;
    }

    /**
     * Create a new Plan with the provided Plan object.
     *
     * @param  Pathway  $pathway The Plan object containing the necessary information.
     * @return void
     */
    public function createPathway(Pathway $pathway)
    {

        DB::beginTransaction();

        try {

            //insert data into plan

            $pathwayEloquent = PathwayMapper::toEloquent($pathway);
            $pathwayEloquent->save();

            $pathwayEloquent->storybooks()->sync(
                collect($pathway->storybooks)->mapWithKeys(function ($storybook, $index) {
                    return [$storybook => ['sequence' => $index + 1]];
                })
            );
            if (request()->hasFile('image') && request()->file('image')->isValid()) {
                $pathwayEloquent->addMediaFromRequest('image')
                    ->toMediaCollection('pathway_img', 'media_storybook');
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($error->getMessage());
        }
    }

    //  update plan
    public function updatePathway(PathwayData $pathwayData)
    {

        DB::beginTransaction();

        try {
            $pathwayDataArray = $pathwayData->toArray();
            $pathwayEloquent = PathwayEloquentModel::query()->findOrFail($pathwayData->id);
            $pathwayEloquent->fill($pathwayDataArray);
            $pathwayEloquent->update();

            $pathwayEloquent->storybooks()->sync(
                collect($pathwayData->storybooks)->mapWithKeys(function ($storybook, $index) {
                    return [$storybook => ['sequence' => $index + 1]];
                })
            );
            if (request()->hasFile('image') && request()->file('image')->isValid()) {

                $old_thumbnail = $pathwayEloquent->getFirstMedia('pathway_img');
                if ($old_thumbnail != null) {
                    $old_thumbnail->forceDelete();
                }
                $pathwayEloquent->addMediaFromRequest('image')->toMediaCollection('pathway_img', 'media_game');
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($error->getMessage());
        }
    }

    public function deletePathway(PathwayEloquentModel $pathway)
    {
        $pathway->delete();
    }

    public function getStudentPathway()
    {
        $pathway_id = request('pathway_id');
        $pathways = PathwayEloquentModel::where('id', $pathway_id)->with('storybooks')->orderBy('id', 'desc')->first();
        $array = [];
        foreach ($pathways->storybooks as $storybook) {
            array_push($array, $storybook->id);
        }
        $storybooks = StoryBookEloquentModel::with(['pathways' => function ($query) use ($pathway_id) {
            $query->where('pathway_id', $pathway_id);
        }])->whereIn('id', $array)->get();
        $total_book_count = count($storybooks);
        $datas = array_chunk($storybooks->toArray(), 2);
        // array_push($datas, []);
        $last_index = end($datas);
        $last_count = count($last_index);
        if (isEvenOdd($last_count) == 'even') {
            array_push($datas, [
                [
                    "id" => "complete",
                    "thumbnail_img" => "/images/box.png"
                ]
            ]);
        } else {
            $last_index[1] = [
                "id" => "complete",
                "thumbnail_img" => "/images/box.png"
            ];
            array_pop($datas);
            array_push($datas, $last_index);
        }
        return [
            "data" => $datas,
            "total" => $total_book_count
        ];
    }
}
