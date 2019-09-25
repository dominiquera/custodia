<?php

use Illuminate\Database\Seeder;

class MonthlyEventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('monthly_events')->insert([
            'month' => "January",
            'title' => 'The New Years Event',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "February",
            'title' => 'The Kitchen Event',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "March",
            'title' => 'Windows and Doors Event',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "April",
            'title' => 'Spring has Sprung',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "May",
            'title' => 'Lawn and Garden Event',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "June",
            'title' => 'Decks, Porches and Balconies',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "July",
            'title' => 'Walls and Floors Event',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "August",
            'title' => 'The Bathroom Event',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "September",
            'title' => 'Seasonal Maintenance Event',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "October",
            'title' => 'Home Safety Month',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "November",
            'title' => 'Winter Prep Month',
        ]);
        DB::table('monthly_events')->insert([
            'month' => "December",
            'title' => 'Custodia Bingo Month!',
        ]);
    }
}
