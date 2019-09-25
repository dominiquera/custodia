<?php

use Illuminate\Database\Seeder;

class WeatherTriggerTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('weather_trigger_types')->insert([
            'name' => '< -4C',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'Remove from list when it\'s raining',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'Humidity levels in below 4',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'Humidity levels in decrease above 6',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'Snow is predicted in the forecast for the day',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'After >4CM Snow has fallen',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'Over 5MM of rain fallen in the past 2 days',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'Humidity is above 6 for a day or more',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'Over 5MM of rain in forcast',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => 'No rain for 14 days',
        ]);
        DB::table('weather_trigger_types')->insert([
            'name' => '-5C',
        ]);
    }
}
