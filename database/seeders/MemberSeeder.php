<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $hasStatus = Schema::hasColumn('members', 'status');

        for ($i = 0; $i < 10; $i++) {
            $data = [
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'phone' => $faker->optional(0.9)->numerify('06########'),
                'email' => $faker->optional(0.9)->safeEmail(),
                'subscription_expiry' => $faker->optional(0.7)->dateTimeBetween('-1 month', '+1 year'),
            ];
            if ($hasStatus) {
                $data['status'] = $faker->randomElement(['active', 'active', 'active', 'inactive']);
            }
            Member::create($data);
        }
    }
}
