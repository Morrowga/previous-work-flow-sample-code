<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\UserEloquentModel;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\ParentEloquentModel;
use Src\BlendedConcept\Student\Infrastructure\EloquentModels\StudentEloquentModel;
use Src\BlendedConcept\Finance\Infrastructure\EloquentModels\SubscriptionEloquentModel;
use Src\BlendedConcept\Organization\Infrastructure\EloquentModels\OrganisationEloquentModel;

class StudentRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = [
            'role_id' => 6,
            'first_name' => 'Student',
            'last_name' => 'One',
            'email' => 'studentone@mail.com',
            'password' => bcrypt('password'),
            'contact_number' => '1234567890',
            'status' => 'ACTIVE',
            'email_verification_send_on' => now(),
            'profile_pic' => 'images/profile/profilefive.png',
        ];

        $userCreate = UserEloquentModel::create($user);

        $subscription = SubscriptionEloquentModel::first();

        $parentData = [
            "user_id" => $userCreate->id,
            "organisation_id" => 1,
            "curr_subscription_id" => $subscription->id,
            "type" => 'B2B'
        ];

        $parentCreate = ParentEloquentModel::create($parentData);

        $studentData = [
            'user_id' => $userCreate->id,
            'organisation_id' => 1,
            'parent_id' => $parentCreate->parent_id,
            'dob' => now(),
            'gender' => 'Male',
            'education_level' => 'G1',
        ];

        $studentCreate = StudentEloquentModel::create($studentData);
    }
}
