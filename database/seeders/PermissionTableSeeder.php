<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\BlendedConcept\User\Domain\Model\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'create_permission',
                "guard_name" => "web"
            ],
            [
                'name' => 'edit_permission',
                "guard_name" => "web"
            ],
            [
                'name' => 'delete_permission',
                "guard_name" => "web"
            ],
            [
                'name' => 'access_permission',
                "guard_name" => "web"
            ],
            [
                'name' => 'create_role',
                "guard_name" => "web"
            ],
            [
                'name' => 'edit_role',
                "guard_name" => "web"
            ],
            [
                'name' => 'show_role',
                "guard_name" => "web"
            ],
            [
                'name' => 'delete_role',
                "guard_name" => "web"
            ],
            [
                'name' => 'access_role',
                "guard_name" => "web"
            ],
            [
                "name" => "access_organization",
                "guard_name" => "web"
            ],
            [
                "name" => "create_organization",
                "guard_name" => "web"
            ],
            [
                "name" => "show_organization",
                "guard_name" => "web"
            ],
            [
                "name" => "edit_organization",
                "guard_name" => "web"
            ],
            [
                "name" => "delete_organization",
                "guard_name" => "web"
            ],
            [
                "name" => "access_user",
                "guard_name" => "web"
            ],
            [
                "name" => "create_user",
                "guard_name" => "web"
            ],
            [
                "name" => "show_user",
                "guard_name" => "web"
            ],
            [
                "name" => "edit_user",
                "guard_name" => "web"
            ],
            [
                "name" => "delete_user",
                "guard_name" => "web"
            ],
            [
                "name" => "access_announcement",
                "guard_name" => "web"
            ],
            [
                "name" => "create_announcement",
                "guard_name" => "web"
            ],
            [
                "name" => "show_announcement",
                "guard_name" => "web"
            ],
            [
                "name" => "edit_announcement",
                "guard_name" => "web"
            ],
            [
                "name" => "delete_announcement",
                "guard_name" => "web"
            ],
            [
                "name" => "access_student",
                "guard_name" => "web"
            ],
            [
                "name" => "create_student",
                "guard_name" => "web"
            ],
            [
                "name" => "show_student",
                "guard_name" => "web"
            ],
            [
                "name" => "edit_student",
                "guard_name" => "web"
            ],
            [
                "name" => "delete_student",
                "guard_name" => "web"
            ],
            [
                "name" => "access_library",
                "guard_name" => "web"
            ],
            [
                "name" => "access_subscriber",
                "guard_name" => "web"
            ],
            [
                "name" => "access_system",
                "guard_name" => "web"
            ],
            [
                "name" => "access_pagebuilder",
                "guard_name" => "web"
            ],

        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
