<?php

declare(strict_types=1);

namespace Src\BlendedConcept\Finance\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\BlendedConcept\Organization\Infrastructure\EloquentModels\OrganizationEloquentModel;

class SubscriptionEloquentModel extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'id',
        'start_date',
        'end_date',
        'payment_date',
        'payment_status',
        'stripe_status',
        'stripe_price',
    ];

    public function b2b_subscriptions()
    {
        return $this->hasMany(B2bSubscriptionEloquentModel::class, 'subscription_id', 'id');
    }

    public function b2b_subscription()
    {
        return $this->hasOne(B2bSubscriptionEloquentModel::class, 'subscription_id', 'id')->orderBy('created_at', 'desc');
    }

    public function b2c_subscriptions()
    {
        return $this->hasMany(B2cSubscriptionEloquentModel::class, 'subscription_id', 'id');
    }

    public function b2c_subscription()
    {
        return $this->hasOne(B2cSubscriptionEloquentModel::class, 'subscription_id', 'id')->with('plan', 'user')->orderBy('created_at', 'desc');
    }

    public function organization()
    {
        return $this->belongsTo(OrganizationEloquentModel::class, 'id', 'curr_subscription_id');
    }

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['name'] ?? false, function ($query, $name) {
            $query->where('name', 'like', '%' . $name . '%');
        });

        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });

        $query->when($filters['filter'] ?? false, function ($query, $filter) {
            if ($filter == 'teachers') {
                $query->join('b2b_subscriptions', 'subscriptions.id', '=', 'b2b_subscriptions.subscription_id')
                    ->orderBy('b2b_subscriptions.num_teacher_license', config('sorting.orderBy'));
            } else if ($filter == 'name') {
                // $query->join('organizations', 'subscriptions.id', '=', 'organizations.curr_subscription_id')->select('organizations.*', 'subscriptions.*')
                //     ->orderBy('organizations.name', config('sorting.orderBy'));
            } else if ($filter == 'plan') {
                // $query->join('plan', 'subscriptions.id', '=', 'b2b_subscriptions.subscription_id')
                //     ->orderBy('b2b_subscriptions.num_student_license', config('sorting.orderBy'));
            } else if ($filter == 'students') {
                $query->join('b2b_subscriptions', 'subscriptions.id', '=', 'b2b_subscriptions.subscription_id')
                    ->orderBy('b2b_subscriptions.num_student_license', config('sorting.orderBy'));
            } else if ($filter == 'storage') {
                $query->join('b2b_subscriptions', 'subscriptions.id', '=', 'b2b_subscriptions.subscription_id')
                    ->orderBy('b2b_subscriptions.storage_limit', config('sorting.orderBy'));
            } else if ($filter == 'user') {
            } else {
                $query->orderBy($filter, config('sorting.orderBy'));
            }
        });
    }
}
