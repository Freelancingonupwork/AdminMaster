<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = bcrypt('admin123');
        $user = User::create([
            'name' => 'Super Admin',
            'type' => "superadmin",
            'email' => "admin00@yopmail.com",
            'mobile' => 9999999999,
            'password' => $password,
            'isUser' => 0,
            'status' => 1
        ]);

        $role = Role::create(['name' => 'superadmin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
