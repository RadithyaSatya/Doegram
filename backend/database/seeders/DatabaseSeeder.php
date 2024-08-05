<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $faker = Factory::create();

        User::factory()->create([
            'name' => 'Admin Tara',
            'username' => 'admin',
            'bio' => 'don\'t waste yor time with useless thing',
            'email' => 'admin@gmail.com',
            'email_verified_at' => $faker->datetime,
            'password' => Hash::make("admin123"),
            'private' => 0,
        ]);

        User::factory(30)->create();
    }
}
