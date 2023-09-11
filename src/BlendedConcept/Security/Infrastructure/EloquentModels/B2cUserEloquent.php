<?php

declare(strict_types=1);

namespace Src\BlendedConcept\Security\Infrastructure\EloquentModels;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Src\BlendedConcept\Organization\Infrastructure\EloquentModels\OrganizationEloquentModel;

class B2cUserEloquentModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'b2c_users';

    protected $fillable = [
        'b2c_user_id',
        'user_id',
        'current_subscription_id',
    ];
}
