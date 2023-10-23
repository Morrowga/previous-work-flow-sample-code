<?php

namespace Src\Auth\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Src\BlendedConcept\Finance\Infrastructure\EloquentModels\B2cSubscriptionEloquentModel;
use Src\BlendedConcept\Finance\Infrastructure\EloquentModels\SubscriptionEloquentModel;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\ParentEloquentModel;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\UserEloquentModel;
use Src\BlendedConcept\Teacher\Infrastructure\EloquentModels\TeacherEloquentModel;

class AuthRepository implements AuthRepositoryInterface
{
    //login
    public function login($request)
    {

        $user = UserEloquentModel::query()->where('email', $request->email)->first();

        return $user;
    }

    //  register b2c register
    public function b2cRegister($request)
    {

        $name = explode('@', $request->email);
        $user = UserEloquentModel::create([
            'name' => $name[0],
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $user->roles()->sync([2]);

        return $user;
    }

    //verification email
    public function verification($id)
    {

        $decode_id = Crypt::decryptString($id);
        $user = UserEloquentModel::findOrFail($decode_id);
        $user->update([
            'email_verified_at' => Carbon::now(),
        ]);

        return $user;
    }

    public function chooseFreePlan($request)
    {
        DB::beginTransaction();
        try {
            $user_type = $request->user_type;
            $userEloquent = (new UserEloquentModel());
            $userEloquent->first_name = $request->first_name;
            $userEloquent->last_name = $request->last_name;
            $userEloquent->contact_number = $request->contact_number;
            $userEloquent->email = $request->email;
            $userEloquent->password = $request->password;
            $userEloquent->email_verification_send_on = null;
            $userEloquent->status = "PENDING";
            $userEloquent->role_id = $user_type == 'Teacher' ? 2 : 7;
            $userEloquent->save();

            $subscriptionEloquent = (new SubscriptionEloquentModel());
            $subscriptionEloquent->start_date = now();
            $subscriptionEloquent->payment_date = now();
            $subscriptionEloquent->payment_status = "PAID";
            $subscriptionEloquent->save();
            if ($user_type == 'Teacher') {
                $teacherEloquent = (new TeacherEloquentModel());
                $teacherEloquent->user_id = $userEloquent->id;
                $teacherEloquent->curr_subscription_id = $subscriptionEloquent->id;
                $teacherEloquent->save();

                $b2cSubEloquent = (new B2cSubscriptionEloquentModel());
                $b2cSubEloquent->subscription_id = $subscriptionEloquent->id;
                $b2cSubEloquent->teacher_id = $teacherEloquent->teacher_id;
                $b2cSubEloquent->plan_id = 1;
                $b2cSubEloquent->save();
            } else {
                $parentEloquent = (new ParentEloquentModel());
                $parentEloquent->user_id = $userEloquent->id;
                $parentEloquent->type = "B2C";
                $parentEloquent->curr_subscription_id = $subscriptionEloquent->id;
                $parentEloquent->save();

                $b2cSubEloquent = (new B2cSubscriptionEloquentModel());
                $b2cSubEloquent->subscription_id = $subscriptionEloquent->id;
                $b2cSubEloquent->parent_id = $parentEloquent->parent_id;
                $b2cSubEloquent->plan_id = 1;
                $b2cSubEloquent->save();
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            dd($ex);
        }
    }

    public function choosePaidPlan($request)
    {
        DB::beginTransaction();
        try {
            $user_type = $request->user_type;
            $userEloquent = (new UserEloquentModel());
            $userEloquent->first_name = $request->first_name;
            $userEloquent->last_name = $request->last_name;
            $userEloquent->contact_number = $request->contact_number;
            $userEloquent->email = $request->email;
            $userEloquent->password = $request->password;
            $userEloquent->email_verification_send_on = now();
            $userEloquent->status = "PENDING";
            $userEloquent->role_id = $user_type == 'Teacher' ? 2 : 7;
            $userEloquent->save();

            if ($user_type == 'Teacher') {
                $teacherEloquent = (new TeacherEloquentModel());
                $teacherEloquent->user_id = $userEloquent->id;
                $teacherEloquent->curr_subscription_id = null;
                $teacherEloquent->save();

                // $b2cSubEloquent = (new B2cSubscriptionEloquentModel());
                // $b2cSubEloquent->subscription_id = $subscriptionEloquent->id;
                // $b2cSubEloquent->teacher_id = $teacherEloquent->teacher_id;
                // $b2cSubEloquent->plan_id = 1;
                // $b2cSubEloquent->save();
            } else {
                $parentEloquent = (new ParentEloquentModel());
                $parentEloquent->user_id = $userEloquent->id;
                $parentEloquent->type = "B2C";
                $parentEloquent->curr_subscription_id = null;
                $parentEloquent->save();

                // $b2cSubEloquent = (new B2cSubscriptionEloquentModel());
                // $b2cSubEloquent->subscription_id = $subscriptionEloquent->id;
                // $b2cSubEloquent->parent_id = $parentEloquent->parent_id;
                // $b2cSubEloquent->plan_id = 1;
                // $b2cSubEloquent->save();
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            dd($ex);
        }
    }
}
