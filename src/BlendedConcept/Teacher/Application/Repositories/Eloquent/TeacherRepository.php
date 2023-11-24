<?php

namespace Src\BlendedConcept\Teacher\Application\Repositories\Eloquent;

use Src\BlendedConcept\Security\Infrastructure\EloquentModels\UserEloquentModel;
use Src\BlendedConcept\Student\Infrastructure\EloquentModels\StudentEloquentModel;
use Src\BlendedConcept\Teacher\Application\DTO\TeacherData;
use Src\BlendedConcept\Teacher\Application\Mappers\TeacherMapper;
use Src\BlendedConcept\Teacher\Domain\Model\Teacher;
use Src\BlendedConcept\Teacher\Domain\Repositories\TeacherRepositoryInterface;
use Src\BlendedConcept\Teacher\Domain\Resources\TeacherResource;
use Src\BlendedConcept\Teacher\Infrastructure\EloquentModels\TeacherEloquentModel;

class TeacherRepository implements TeacherRepositoryInterface
{
    /***
     *
     *
     *
     */
    public function getTeachers($filters = [])
    {
        // dd('hello');
        //set roles
        $users = TeacherResource::collection(UserEloquentModel::filter($filters)
            ->where('organisation_id', auth()->user()->organisation_id)
            ->with('roles')
            ->whereHas('roles', function ($query) {
                return $query->where('name', 'Teacher');
            })
            ->orderBy('id', 'desc')
            ->paginate($filters['perPage'] ?? 10));

        return $users;
    }

    public function CreateTeacher(Teacher $teacher)
    {

        $userEloquent = TeacherMapper::toEloquent($teacher);
        //verify teacher just now
        $userEloquent->email_verified_at = now();
        $userEloquent->save();
        if (request()->hasFile('image') && request()->file('image')->isValid()) {
            $userEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_teachers');
        }

        $userEloquent->roles()->sync(request('role'));
    }

    //  update user
    public function updateTeacher(TeacherData $teacherData)
    {

        $teacherArray = $teacherData->toArray();
        $updateUserEloquent = UserEloquentModel::query()->findOrFail($teacherData->teacher_id);
        $updateUserEloquent->fill($teacherArray);
        $updateUserEloquent->save();

        //  delete image if reupload or insert if does not exit
        if (request()->hasFile('image') && request()->file('image')->isValid()) {

            $old_image = $updateUserEloquent->getFirstMedia('image');
            if ($old_image != null) {
                $old_image->delete();

                $updateUserEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_teachers');
            } else {

                $updateUserEloquent->addMediaFromRequest('image')->toMediaCollection('image', 'media_teachers');
            }
        }

        $updateUserEloquent->roles()->sync(request('role'));
    }

    public function getOrgTeacherStudents($filters)
    {
        $role_name = auth()->user()->role->name;

        $classroom_ids = [];
        if ($role_name == 'BC Super Admin' || $role_name == 'BC Staff' || $role_name == 'Organisation Admin') {
            $teacher = UserEloquentModel::find(auth()->user()->id);
        } else {
            $teachers = TeacherEloquentModel::with('classrooms')->where('user_id', auth()->user()->id)->first();

            foreach ($teachers->classrooms as $classroom) {
                array_push($classroom_ids, $classroom->id);
            }
        }

        $students = StudentEloquentModel::filter($filters)
            ->where('organisation_id', auth()->user()->organisation_id)
            ->whereHas('classrooms', function ($query) use ($classroom_ids) {
                $query->whereIn('id', $classroom_ids);
            })
            ->with('user', 'disability_types', 'parent')->paginate($filters['perPage'] ?? 10);
        return $students;
    }
}
