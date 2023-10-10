<?php

namespace Src\BlendedConcept\StoryBook\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\BlendedConcept\StoryBook\Application\DTO\StoryBookVersionData;
use Src\BlendedConcept\StoryBook\Application\Mappers\ReviewMapper;
use Src\BlendedConcept\StoryBook\Application\Mappers\StoryBookVersionMapper;
use Src\BlendedConcept\StoryBook\Domain\Model\Entities\Review;
use Src\BlendedConcept\StoryBook\Domain\Model\Entities\StoryBookVersion;
use Src\BlendedConcept\StoryBook\Domain\Repositories\StoryBookVersionRepositoryInterface;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\StoryBookVersionEloquentModel;

class StoryBookVersionRepository implements StoryBookVersionRepositoryInterface
{
    public function getStoryBooksWithVersions()
    {
    }

    /****
     *  In the funciton we have to create storyversion that will copy from original storybooks
     *  and b2c and b2b techer can update the setiting on the storybooks
     *  @hareom284
     *
     */

    public function createStoryBookVersion(StoryBookVersion $storyBookVersion)
    {

        DB::beginTransaction();
        try {
            $teacher_id = auth()->user()->b2bUser->teacher_id;
            $storyBookVersionEloquent = StoryBookVersionMapper::toEloquent($storyBookVersion);
            $storyBookVersionEloquent->teacher_id = $teacher_id;
            $storyBookVersionEloquent->save();
            DB::commit();
        } catch (\Exception $error) {

            DB::rollBack();
            dd($error->getMessage());
        }
    }

    public function updateStoryBookVersion(StoryBookVersionData $storyBookVersionData)
    {
        DB::beginTransaction();

        try {
            $storybookVersionArray = $storyBookVersionData->toArray();
            $rewardEloquent = StoryBookVersionEloquentModel::query()->findOrFail($storyBookVersionData->id);
            $rewardEloquent->fill($storybookVersionArray);
            $rewardEloquent->update();

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            // Handle any exceptions and display the error message
            dd($error->getMessage());
        }
    }

    public function deleteStoryBookVersion()
    {
    }

    public function assigmentAssigment()
    {

        DB::beginTransaction();
        try {
            $storybookversion = StoryBookVersionEloquentModel::find(request()->storybook_version_id);
            $storybookversion->storybook_assigments()->sync(request()->student_ids);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            dd($error->getMessage());
        }
    }

    public function bookreview(Review $review)
    {

        DB::beginTransaction();
        try {
            $createEloquent = ReviewMapper::toEloquent($review);
            $createEloquent->given_by_user_id = auth()->user()->id;
            $createEloquent->given_on = now();
            $createEloquent->save();
            DB::commit();
        } catch (\Exception $error) {
            dd($error->getMessage());
            DB::rollBack();
        }
    }
}
