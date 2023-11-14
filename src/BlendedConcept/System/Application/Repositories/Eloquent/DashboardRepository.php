<?php

namespace Src\BlendedConcept\System\Application\Repositories\Eloquent;

use Src\BlendedConcept\Organisation\Infrastructure\EloquentModels\OrganisationEloquentModel;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\UserEloquentModel;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\GameEloquentModel;
use Src\BlendedConcept\StoryBook\Infrastructure\EloquentModels\StoryBookEloquentModel;
use Src\BlendedConcept\Student\Infrastructure\EloquentModels\StudentEloquentModel;
use Src\BlendedConcept\Survey\Infrastructure\EloquentModels\SurveyEloquentModel;
use Src\BlendedConcept\System\Domain\Repositories\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getRecentOrganisations($filters)
    {
        $organisations = OrganisationEloquentModel::filter($filters)->paginate($filters['perPage'] ?? 10);
        return $organisations;
    }

    public function getRecentUsers($filters)
    {
        $users = UserEloquentModel::filter($filters)->with('role')->paginate($filters['perPage'] ?? 10);
        return $users;
    }

    public function getRecentBooks()
    {
        $books = StoryBookEloquentModel::with(['learningneeds', 'themes', 'disability_types', 'devices', 'tags'])
            ->orderBy('id', 'desc')->paginate(10);
        return $books;
    }

    public function getRecentGames()
    {
        $games = GameEloquentModel::with(['tags', 'disabilityTypes', 'devices'])->orderBy('id', 'desc')->paginate(10);
        return $games;
    }

    public function getRecentSurveys($filters)
    {
        $surveys = SurveyEloquentModel::filter($filters)->where('type', 'USEREXP')->orderBy('id', 'desc')->with(['survey_settings'])->paginate($filters['perPage'] ?? 10);
        return $surveys;
    }

    public function getRecentStudents($filters)
    {
        $curr_role_name = auth()->user()->role->name;
        $query =  StudentEloquentModel::with('user', 'teachers', 'organisation', 'disability_types', 'parent')
            ->filter($filters)
            ->orderBy('student_id', 'desc');

        $students = [];
        if ($curr_role_name == "BC Subscriber" || $curr_role_name == "Teacher") {
            $user_type = auth()->user()->b2bUser ? "Teacher" : "Parent";
            if ($user_type == "Teacher") {
                $user_id = auth()->user()->id;
                $students = $query->whereHas('teachers', function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id);
                })->paginate($filters['perPage'] ?? 10);
            } else {
                $user_id = auth()->user()->parents->parent_id;
                $students = $query->where('parent_id', $user_id)->paginate($filters['perPage'] ?? 10);
            }
        }
        // else if ($curr_role_name == "B2B Parent" || $curr_role_name == "B2C Parent" || $curr_role_name == "Both Parent") {
        //     $user_id = auth()->user()->parents->parent_id;
        //     $students = $query->where('parent_id', $user_id)->paginate($filters['perPage'] ?? 10);
        // }
        return $students;
    }

    public function getParentStudentList()
    {
        $auth = auth()->user();
        $parent = $auth->parents;
        if ($parent) {
            $students = StudentEloquentModel::whereHas('parent', function ($query) use ($parent) {
                $query->where('parent_id', $parent->parent_id);
            })->with(['user'])->get();
        } else {
            $students = StudentEloquentModel::whereHas('teachers', function ($query) use ($parent) {
                $query->where('teachers.teacher_id', auth()->user()->b2bUser->teacher_id);
            })->with(['user'])->get();
        }
        return $students;
    }
}
