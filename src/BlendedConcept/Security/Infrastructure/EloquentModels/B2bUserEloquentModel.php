<?php

declare(strict_types=1);

namespace Src\BlendedConcept\Security\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Src\BlendedConcept\Organisation\Infrastructure\EloquentModels\OrganisationEloquentModel;

class B2bUserEloquentModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'b2b_users';

    protected $fillable = [
        'b2b_user_id',
        'user_id',
        'organisation_id',
        'allocated_storage_limit',
        'has_full_library_access',
    ];

    public function users()
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id');
    }

    public function organisation()
    {
        return $this->hasOne(OrganisationEloquentModel::class, 'id', 'organisation_id');
    }
}
