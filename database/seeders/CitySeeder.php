<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $citiesNames = ['cairo', 'alexandria', 'mdbansoura'];

        foreach ($citiesNames as $cityName) {
            City::create([
                'city_name' => $cityName
            ]);
        }
    }
}
