<?php

namespace Src\BlendedConcept\Student\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\BlendedConcept\Student\Application\DTO\PlaylistData;
use Src\BlendedConcept\Student\Application\Mappers\PlaylistMapper;
use Src\BlendedConcept\Student\Domain\Model\Entities\Playlist;
use Src\BlendedConcept\Student\Domain\Repositories\PlaylistRepositoryInterface;
use Src\BlendedConcept\Student\Domain\Resources\PlaylistResource;
use Src\BlendedConcept\Student\Infrastructure\EloquentModels\PlaylistEloquentModel;
use Src\BlendedConcept\Student\Infrastructure\EloquentModels\StudentEloquentModel;

class PlaylistRepository implements PlaylistRepositoryInterface
{
    public function getPlaylist($filters = [])
    {
        $auth = auth()->user()->role->name;
        if ($auth == 'B2B Parent') {
            $parent = auth()->user()->parents;
            $student = StudentEloquentModel::where('parent_id', $parent->parent_id)->first();
            $user_id = $student->teachers()->pluck('teachers.teacher_id');
            $playlists = PlaylistResource::collection(PlaylistEloquentModel::filter($filters)
                ->with(['storybooks', 'student.user'])
                ->whereIn('teacher_id', $user_id)
                ->orderBy('id', 'desc')
                ->paginate($filters['perPage'] ?? 10));
        } elseif ($auth == 'BC Subscriber') {
            if(auth()->user()->b2bUser == null){
                $parent = auth()->user()->parents;
                $user_id = $parent->parent_id;
                $playlists = PlaylistResource::collection(PlaylistEloquentModel::filter($filters)
                    ->with(['storybooks', 'student.user'])
                    ->where('parent_id', $user_id)
                    ->orderBy('id', 'desc')
                    ->paginate($filters['perPage'] ?? 10));
            } else {
                $teacher = auth()->user()->b2bUser;
                $user_id = $teacher->teacher_id;
                $playlists = PlaylistResource::collection(PlaylistEloquentModel::filter($filters)
                    ->with(['storybooks', 'student.user'])
                    ->where('teacher_id', $user_id)
                    ->orderBy('id', 'desc')
                    ->paginate($filters['perPage'] ?? 10));
            }
        } else {
            $user_id = [auth()->user()->b2bUser->teacher_id];
            $playlists = PlaylistResource::collection(PlaylistEloquentModel::filter($filters)
                ->with(['storybooks', 'student.user'])
                ->whereIn('teacher_id', $user_id)
                ->orderBy('id', 'desc')
                ->paginate($filters['perPage'] ?? 10));
        }

        // elseif ($auth == 'Both Parent') {
        //     $parent = auth()->user()->parents;
        //     $user_id = $parent->parent_id;
        //     $student = StudentEloquentModel::with('teachers')->where('parent_id', $parent->parent_id)->first();
        //     if ($parent->organisation_id) {
        //         $teacher = $student->classrooms()->with('teachers')->get()->pluck('teachers')->flatten();
        //         $teacher_id = $teacher->map(function ($teacher) {
        //             return $teacher->teacher_id;
        //         });
        //     } else {
        //         $teacher_id = $student->teachers()->pluck('teachers.teacher_id');
        //     }
        //     $playlists = PlaylistResource::collection(PlaylistEloquentModel::filter($filters)
        //         ->with(['storybooks', 'student.user'])
        //         ->where('parent_id', $user_id)
        //         ->orWhereIn('teacher_id', $teacher_id)
        //         ->orderBy('id', 'desc')
        //         ->paginate($filters['perPage'] ?? 10));
        // }


        return $playlists;
    }

    public function showPlaylist($id)
    {
        $playlist = new PlaylistResource(PlaylistEloquentModel::where('id', $id)
            ->with(['storybooks', 'student.user'])
            ->orderBy('id', 'desc')
            ->first());

        return $playlist;
    }

    public function createPlaylist(Playlist $playlist)
    {
        DB::beginTransaction();

        try {
            $playlistEloquent = PlaylistMapper::toEloquent($playlist);
            $playlistEloquent->save();

            if (request()->hasFile('image') && request()->file('image')->isValid()) {
                $playlistEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_playlists');
            }

            if ($playlistEloquent->getMedia('image')->isNotEmpty()) {
                $playlistEloquent->playlist_photo = $playlistEloquent->getMedia('image')[0]->original_url;
                $playlistEloquent->update();
            }

            $storybooksCollection = collect(request()->storybooks);
            $storybooksCollection = $storybooksCollection->count();

            if ($storybooksCollection > 0) {
                $playlistEloquent->storybooks()->attach(request()->storybooks);
            }

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($error->getMessage());
        }
    }

    public function updatePlaylist(PlaylistData $playlistData)
    {
        DB::beginTransaction();

        try {

            $playlistArray = $playlistData->toArray();
            $updatePlaylistEloquent = PlaylistEloquentModel::query()->findOrFail($playlistData->id);
            $updatePlaylistEloquent->fill($playlistArray);
            $updatePlaylistEloquent->update();

            //  delete image if reupload or insert if does not exit
            if (request()->hasFile('image') && request()->file('image')->isValid()) {
                $old_image = $updatePlaylistEloquent->getFirstMedia('image');
                if ($old_image != null) {
                    $old_image->forceDelete();
                }

                $newMediaItem = $updatePlaylistEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_playlists');

                if ($newMediaItem->getUrl()) {
                    $updatePlaylistEloquent->playlist_photo = $newMediaItem->getUrl();
                    $updatePlaylistEloquent->update();
                }
            }

            $playlistCollection = collect(request()->storybooks);

            $playlistLength = $playlistCollection->count();

            if ($playlistLength > 0) {
                $updatePlaylistEloquent->storybooks()->detach();

                $updatePlaylistEloquent->storybooks()->attach(request()->storybooks);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($error->getMessage());
        }
    }

    public function delete(int $playlist_id): void
    {
        $playlist = PlaylistEloquentModel::query()->findOrFail($playlist_id);
        $playlist->clearMediaCollection('image'); // Replace with the actual collection name
        $playlist->delete();
    }

    public function getStorybooksForPlaylist($filters)
    {
    }
}
