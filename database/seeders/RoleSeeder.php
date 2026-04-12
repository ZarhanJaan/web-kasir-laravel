<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['owner', 'admin', 'kasir'];

        foreach ($roles as $role) {
            Role::updateOrCreate(['role' => $role]);
        }
    }
}
