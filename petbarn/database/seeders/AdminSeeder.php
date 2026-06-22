<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Full system access'],
        );

        Branch::query()->firstOrCreate(
            ['name' => 'Jdeideh'],
            [
                'type' => 'both',
                'is_warehouse' => true,
                'address' => 'Jdeideh, Lebanon',
                'phone' => null,
            ],
        );

        Branch::query()->firstOrCreate(
            ['name' => 'Ajaltoun'],
            [
                'type' => 'retail',
                'is_warehouse' => false,
                'address' => 'Ajaltoun, Lebanon',
                'phone' => null,
            ],
        );

        Branch::query()->firstOrCreate(
            ['name' => 'Zouk'],
            [
                'type' => 'retail',
                'is_warehouse' => false,
                'address' => 'Zouk, Lebanon',
                'phone' => null,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@petbarn.lb'],
            [
                'name' => 'PetBarn Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'branch_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
