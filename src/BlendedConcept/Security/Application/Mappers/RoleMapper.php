<?php

namespace Src\BlendedConcept\Security\Application\Mappers;

use Illuminate\Http\Request;
use Src\BlendedConcept\Security\Domain\Model\Role;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\RoleEloquentModel;

class RoleMapper
{
    public static function fromRequest(Request $request, $role_id = null): Role
    {
        return new Role(
            id: $role_id,
            name: $request->name,
            description: $request->description,
        );
    }

    public static function toEloquent(Role $role): RoleEloquentModel
    {
        $RoleEloquent = new RoleEloquentModel();

        if ($role->id) {
            $RoleEloquent = RoleEloquentModel::query()->findOrFail($role->id);
        }

        $RoleEloquent->name = $role->name;
        $RoleEloquent->description = $role->description;
        return $RoleEloquent;
    }
}
