<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new CitySeeder())->run(); // Seed city data
        (new AdminSeeder())->run(); // Seed Admin data
        (new UserSeeder())->run(); // Seed data client users data and store users data
    }
}
