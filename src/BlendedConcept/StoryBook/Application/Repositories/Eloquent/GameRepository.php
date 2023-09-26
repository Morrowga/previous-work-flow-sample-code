<?php

namespace Src\BlendedConcept\StoryBook\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\BlendedConcept\StoryBook\Application\DTO\GameData;
use Src\BlendedConcept\StoryBook\Application\Mappers\GameMapper;
use Src\BlendedConcept\StoryBook\Domain\Model\Entities\Game;
use Src\BlendedConcept\StoryBook\Domain\Repositories\GameRepositoryInterface;
use Src\BlendedConcept\StoryBook\Domain\Resources\GameResource;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\GameEloquentModel;

class GameRepository implements GameRepositoryInterface
{
    //get all games
    public function getGameList($filters)
    {
        $games = GameResource::collection(GameEloquentModel::filter($filters)
            ->with(['tags', 'disabilityTypes', 'devices'])
            ->orderBy('id', 'desc')
            ->paginate($filters['perPage'] ?? 10));

        return $games;
    }

    /**
     * Create a new game with the provided game object.
     *
     * @param  Game  $game The organization object containing the necessary information.
     * @return void
     */
    public function createGame(Game $game)
    {
        DB::beginTransaction();
        try {
            // Insert data into the game
            $gameEloquent = GameMapper::toEloquent($game);
            $gameEloquent->save();

            // Upload the game's image if provided
            if (request()->hasFile('thumb') && request()->file('thumb')->isValid()) {
                $gameEloquent->addMediaFromRequest('thumb')->toMediaCollection('thumbnail', 'media_game');
            }

            if (request()->hasFile('game') && request()->file('game')->isValid()) {
                $gameEloquent->addMediaFromRequest('game')->toMediaCollection('game_file', 'media_game');
            }

            if ($gameEloquent->getMedia('thumbnail')->isNotEmpty() && $gameEloquent->getMedia('game_file')->isNotEmpty()) {
                $gameEloquent->thumbnail = $gameEloquent->getMedia('thumbnail')[0]->original_url;
                $gameEloquent->game_file = $gameEloquent->getMedia('game_file')[0]->original_url;
                $gameEloquent->update();
            }

            $tagCollection = collect(request()->tags);
            $disabilityCollection = collect(request()->disability_type_id);
            $deviceCollection = collect(request()->devices);

            $tagLength = $tagCollection->count();
            $disabilityLength = $disabilityCollection->count();
            $deviceLength = $deviceCollection->count();

            if ($tagLength > 0) {
                $gameEloquent->associateTags(request()->tags);
            }

            if ($deviceLength > 0) {
                $gameEloquent->devices()->attach(request()->devices);
            }

            if ($disabilityLength > 0) {
                // Attach disability types with the game
                $gameEloquent->disabilityTypes()->attach(request()->disability_type_id);
            }

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            dd($error->getMessage());
        }
    }

    //update game
    public function updateGame(GameData $game)
    {
        DB::beginTransaction();
        try {
            $gameArray = $game->toArray();
            $gameEloquent = GameEloquentModel::query()->find($game->id);
            $gameEloquent->fill($gameArray);
            $gameEloquent->update();

            if (request()->hasFile('thumb') && request()->file('thumb')->isValid()) {
                $old_thumbnail = $gameEloquent->getFirstMedia('thumbnail');
                if ($old_thumbnail != null) {
                    $old_thumbnail->forceDelete();
                }

                $newThumbMedia = $gameEloquent->addMediaFromRequest('thumb')->toMediaCollection('thumbnail', 'media_game');

                if ($newThumbMedia->getUrl()) {
                    $gameEloquent->thumbnail = $newThumbMedia->getUrl();
                    $gameEloquent->update();
                }
            }

            if (request()->hasFile('game') && request()->file('game')->isValid()) {

                $old_game = $gameEloquent->getFirstMedia('game_file');
                if ($old_game != null) {
                    $old_game->forceDelete();
                }

                $newGameMedia = $gameEloquent->addMediaFromRequest('game')->toMediaCollection('game_file', 'media_game');

                if ($newGameMedia->getUrl()) {
                    $gameEloquent->game_file = $newGameMedia->getUrl();
                    $gameEloquent->update();
                }
            }

            $tagCollection = collect(request()->tags);
            $disabilityCollection = collect(request()->disability_type_id);
            $deviceCollection = collect(request()->devices);

            $tagLength = $tagCollection->count();
            $disabilityLength = $disabilityCollection->count();
            $deviceLength = $deviceCollection->count();

            if ($tagLength > 0) {
                $gameEloquent->tags()->detach();
                // Attach new tags (assuming $request contains the new tag IDs)
                $gameEloquent->associateTags(request()->tags);
            }

            if ($deviceLength > 0) {
                $gameEloquent->devices()->detach();

                $gameEloquent->devices()->attach(request()->devices);
                // Attach new tags (assuming $request contains the new tag IDs)
            }

            if ($disabilityLength > 0) {
                $gameEloquent->disabilityTypes()->detach();

                $gameEloquent->disabilityTypes()->attach(request()->disability_type_id);
                // Attach new tags (assuming $request contains the new tag IDs)
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();

            return $error->getMessage();
        }
    }
}
