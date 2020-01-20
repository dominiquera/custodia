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
            'name' => 'Wheelchair Ramp',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Fireplace',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Pool',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Chair Lift',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Entry Stairs',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Accessible Bathroom',
        ]);
        DB::table('home_features')->insert([
            'name' => 'Other',
        ]);
    }
}
