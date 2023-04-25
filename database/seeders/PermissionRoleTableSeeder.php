<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\BlendedConcept\User\Domain\Model\Role;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['name' => 'BC Super Admin', 'guard_name' => 'web'],
            ['name' => "BC Subscriber", "guard_name" => "web"],
        ];
        foreach ($datas as $data) {
            $role = Role::create($data);
            $role->permissions()->sync([25, 26, 27, 28, 29]);
        }
    }
}
