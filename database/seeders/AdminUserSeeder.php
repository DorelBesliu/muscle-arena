<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('app.admin.email')],
            [
                'name' => 'Administrator',
                'password' => Hash::make(config('app.admin.password')),
                'role' => 'admin',
            ]
        );
    }
}
