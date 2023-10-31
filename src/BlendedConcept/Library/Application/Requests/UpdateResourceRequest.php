<?php

namespace Src\BlendedConcept\Library\Application\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Src\BlendedConcept\Library\Infrastructure\EloquentModels\MediaEloquentModel;
use Src\BlendedConcept\Teacher\Infrastructure\EloquentModels\TeacherEloquentModel;

class UpdateResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (auth()->user()->role->name == 'Teacher') {
            return [
                'filename' => [
                    'required',
                ],
                'file' => [
                    'nullable', // The file field can be nullable (no file required)
                    'file',
                    'max:' . $this->checkTeacherStorageLimit(), // Validate file size against the allocated storage size
                ],
            ];
        }

        if (auth()->user()->role->name == 'BC Subscriber') {
            return [
                'filename' => [
                    'required',
                ],
                'file' => [
                    'nullable', // The file field can be nullable (no file required)
                    'file',
                    'max:' . $this->checkB2CTeacherStorageLimit(), // Validate file size against the allocated storage size
                ],
            ];
        }

        if (auth()->user()->role->name == 'B2C Parent' || auth()->user()->role->name == 'Both Parent') {
            return [
                'filename' => [
                    'required',
                ],
                'file' => [
                    'required',
                    'file',
                    'max:' . $this->checkB2CParentStorageLimit(), // Validate file size against the allocated storage size
                ],
            ];
        }

        return [
            'filename' => [
                'required',
            ],
            'file' => [
                'nullable', // The file field can be nullable (no file required)
                'file', // Ensure it's a file
                'max:' . $this->checkOrgStorageLimit() // Validate file size against the storage size
            ],
        ];
    }

    public function messages()
    {
        return [
            'filename' => 'Filename is required',
            'file.max' => 'File size exceeds your allocated storage.',
        ];
    }

    public function checkOrgStorageLimit()
    {
        $subscription = auth()->user()->org_admin->organisation->subscription != null ? auth()->user()->org_admin->organisation->subscription->b2b_subscription : null;
        if ($subscription === null) {
            $leftStorageLimit = 1; // Default value when the subscription is empty
        } else {
            $totalStorage = $subscription->storage_limit * 1024;
            $organisation_id = auth()->user()->org_admin->organisation->id;
            $teacherStorages = TeacherEloquentModel::where('organisation_id', $organisation_id)->sum('allocated_storage_limit');
            // $organisationEloquent = OrganisationEloquentModel::where('org_admin_id', $org_admin->org_admin_id)->first();
            $usedStorage = MediaEloquentModel::where('collection_name', 'videos')
                ->where('organisation_id', $organisation_id)
                ->where('teacher_id', null)
                ->where('status', 'active')
                ->sum('size');

            $teacherStorageKiloBytes = $teacherStorages * 1024;

            $totalStorageLimit = $totalStorage - $teacherStorageKiloBytes;
            $usedKilobytes = $usedStorage / 1024;

            $leftStorageLimit = $totalStorageLimit - $usedKilobytes;
        };

        return (int) $leftStorageLimit;
    }

    public function checkTeacherStorageLimit()
    {
        $allocatedStorage = auth()->user()->b2bUser->allocated_storage_limit === null ?  0 : (int) auth()->user()->b2bUser->allocated_storage_limit * 1024; // Retrieve the allocated storage size for the user
        $teacherEloquent = auth()->user()->b2bUser;
        $userEloquentModel = auth()->user();
        if ($allocatedStorage > 0) {
            $usedStorage = MediaEloquentModel::where(function ($query) use ($teacherEloquent, $userEloquentModel) {
                $query->where('collection_name', 'videos')
                    ->where('organisation_id', $teacherEloquent->organisation_id)
                    ->where(function ($innerQuery) use ($userEloquentModel) {
                        $innerQuery->where('teacher_id', $userEloquentModel->id);
                    })
                    ->whereIn('status', ['active', 'requested']);
            })
                ->sum('size');

            if ($usedStorage > 0) {
                $usedKilobytes = $usedStorage / 1024;

                $leftStorageLimit = $allocatedStorage - $usedKilobytes;

                return (int) $leftStorageLimit;
            }
            return (int) $allocatedStorage;
        }

        return 1;
    }

    public function checkB2CTeacherStorageLimit()
    {
        $storage = auth()->user()->b2bUser->subscription == null ? 0 : (auth()->user()->b2bUser->subscription->b2c_subscription == null ? 0 : (auth()->user()->b2bUser->subscription->b2c_subscription->plan == null ? 0 : auth()->user()->b2bUser->subscription->b2c_subscription->plan->storage_limit * 1024));
        $storage = (int) $storage;
        // Retrieve the allocated storage size for the user
        $teacherEloquent = auth()->user()->b2bUser;
        $userEloquentModel = auth()->user();
        if ($storage > 0) {
            $usedStorage = MediaEloquentModel::where(function ($query) use ($teacherEloquent, $userEloquentModel) {
                $query->where('collection_name', 'videos')
                    ->where('teacher_id', $userEloquentModel->id)
                    ->whereIn('status', ['active', 'requested']);
                })
                ->sum('size');

            if ($usedStorage > 0) {
                $usedKilobytes = $usedStorage / 1024;

                $leftStorageLimit = $storage - $usedKilobytes;

                return (int) $leftStorageLimit;
            }

            return $storage;
        }

        return 1;
    }

    public function checkB2CParentStorageLimit()
    {
        $storage = auth()->user()->parents->subscription == null ? 0 : (auth()->user()->parents->subscription->b2c_subscription == null ? 0 : (auth()->user()->parents->subscription->b2c_subscription->plan == null ? 0 : auth()->user()->parents->subscription->b2c_subscription->plan->storage_limit * 1024));
        $storage = (int) $storage;
        // Retrieve the allocated storage size for the user
        $parentEloquent = auth()->user()->parents;
        $userEloquentModel = auth()->user();
        if ($storage > 0) {
            $usedStorage = MediaEloquentModel::where(function ($query) use ($parentEloquent, $userEloquentModel) {
                $query->where('collection_name', 'videos')
                    ->where('teacher_id', $userEloquentModel->id)
                    ->whereIn('status', ['active', 'requested']);
                })
                ->sum('size');

            if ($usedStorage > 0) {
                $usedKilobytes = $usedStorage / 1024;

                $leftStorageLimit = $storage - $usedKilobytes;

                return (int) $leftStorageLimit;
            }

            return $storage;
        }

        return 1;
    }
}
