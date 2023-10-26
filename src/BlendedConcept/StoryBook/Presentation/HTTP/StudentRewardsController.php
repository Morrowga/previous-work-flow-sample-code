<?php

namespace Src\BlendedConcept\StoryBook\Presentation\HTTP;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\BlendedConcept\StoryBook\Application\DTO\RewardData;
use Src\BlendedConcept\StoryBook\Application\Requests\UpdateStickerRequest;
use Src\BlendedConcept\StoryBook\Application\UseCases\Queries\GetStickerRollData;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\RewardEloquentModel;
use Src\BlendedConcept\StoryBook\Application\UseCases\Commands\StudentRewards\OwnStickerCommand;
use Src\BlendedConcept\StoryBook\Application\UseCases\Commands\StudentRewards\DropStickerCommand;
use Src\BlendedConcept\StoryBook\Application\UseCases\Queries\Rewards\GetStudentsReward;

class StudentRewardsController extends Controller
{
    public function index()
    {
        try {
            $stickers = (new GetStudentsReward())->handle();

            return Inertia::render(config('route.student-rewards'), [
                "stickers" => $stickers['stickers'],
                "placed_stickers" => $stickers['placed_stickers']
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('errorMessage', $e->getMessage());
        }
    }

    public function store()
    {
        try {
            return Inertia::render(config('route.reward-store'));
        } catch (Exception $e) {
            return redirect()->back()->with('errorMessage', $e->getMessage());
        }
    }

    public function beLucky()
    {
        try {
            return Inertia::render(config('route.be-lucky'));
        } catch (Exception $e) {
            return redirect()->back()->with('errorMessage', $e->getMessage());
        }
    }

    public function buySticker()
    {
        try {
            $stickers = RewardEloquentModel::where('status', 'ACTIVE')->get();
            return Inertia::render(config('route.buy-sticker'), [
                "stickers" => $stickers
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('errorMessage', $e->getMessage());
        }
    }

    public function ownSticker(RewardEloquentModel $reward)
    {
        try {
            $sticker = (new OwnStickerCommand($reward));
            $sticker->execute();
            return redirect()->back()->with('successMessage', 'Own Sticker Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorMessage', $e->getMessage());
        }
    }

    public function dropSticker(UpdateStickerRequest $request, RewardEloquentModel $reward)
    {
        try {
            $updateSticker = RewardData::fromRequest($request, $reward->id);
            $sticker = (new DropStickerCommand($updateSticker));
            $sticker->execute();
            return redirect()->back()->with('successMessage', 'Drop Sticker Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('errorMessage', $e->getMessage());
        }
    }


    public function stickerRoll(Request $request)
    {
        $stickers = (new GetStickerRollData($request->query('count')))->handle();

        return response()->json($stickers);
    }
}
