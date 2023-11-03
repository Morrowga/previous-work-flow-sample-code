<?php

namespace Src\BlendedConcept\Disability\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\BlendedConcept\Disability\Application\DTO\DeviceData;
use Src\BlendedConcept\Disability\Application\Mappers\DeviceMapper;
use Src\BlendedConcept\Disability\Domain\Model\Entities\Device;
use Src\BlendedConcept\Disability\Domain\Repositories\DeviceRepositoryInterface;
use Src\BlendedConcept\Disability\Domain\Resources\DeviceResource;
use Src\BlendedConcept\Disability\Infrastructure\EloquentModels\DeviceEloquentModel;
use Src\BlendedConcept\Organisation\Infrastructure\EloquentModels\StudentEloquentModel;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\StoryBookEloquentModel;

class DeviceRepository implements DeviceRepositoryInterface
{
    public function getDevices($filters)
    {

        $devices = DeviceResource::collection(DeviceEloquentModel::filter($filters)->with('disabilityTypes')->orderBy('id', 'desc')->paginate($filters['perPage'] ?? 10));

        return $devices;
    }

    public function createDevice(Device $device)
    {
        DB::beginTransaction();

        try {
            $deviceEloquent = DeviceMapper::toEloquent($device);
            $deviceEloquent->save();

            $disabilityCollection = collect($device->disability_types);

            $disabilityLength = $disabilityCollection->count();
            if ($disabilityLength > 0) {
                $deviceEloquent->disabilityTypes()->attach($device->disability_types);
            }

            if($device->storybook_id != null){
                $deviceEloquent->books()->sync([$device->storybook_id]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($exception->getMessage());
        }
    }

    public function updateDevice(DeviceData $deviceData)
    {
        DB::beginTransaction();
        try {
            $deviceArray = $deviceData->toArray();
            $deviceEloquent = DeviceEloquentModel::findOrFail($deviceData->id);
            $deviceEloquent->fill($deviceArray);
            $deviceEloquent->update();

            $disabilityCollection = collect($deviceData->disability_types);
            $disabilityLength = $disabilityCollection->count();

            if ($disabilityLength > 0) {
                $deviceEloquent->disabilityTypes()->detach();

                $deviceEloquent->disabilityTypes()->attach($deviceData->disability_types);
                // Attach new tags (assuming $request contains the new tag IDs)
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($exception->getMessage());
        }
    }

    public function deleteDevice(DeviceEloquentModel $device)
    {
        DB::beginTransaction();
        try {
            // $device->delete();
            $device->update([
                'status' => request('status')
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($exception->getMessage());
        }
    }

    public function getDevicesWithoutPagination()
    {

        $devices = DeviceResource::collection(DeviceEloquentModel::with('disabilityTypes')->orderBy('id', 'desc')->get());

        return $devices;
    }

    public function setDevice(StudentEloquentModel $student, DeviceEloquentModel $device)
    {
        DB::beginTransaction();
        try {
            $student->device_id = $device->id;
            $student->update();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            config('app.env') == 'production'
                ? throw new \Exception('Something Wrong! Please try again.')
                : throw new \Exception($exception->getMessage());
        }
    }
    public function getSimpleBooks()
    {
        $books = StoryBookEloquentModel::take(5)->get();
        return $books;
    }
}
