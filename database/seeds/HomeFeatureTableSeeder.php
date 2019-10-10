<?php

use Illuminate\Database\Seeder;

class HomeFeatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('home_features')->insert([
            'name' => 'No Features',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Deck',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Porch',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Balcony',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Walkway',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Fireplace',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Pool',
        ]);
    }
}
