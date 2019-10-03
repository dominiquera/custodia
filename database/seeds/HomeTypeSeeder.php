<?php

use Illuminate\Database\Seeder;

class HomeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('home_types')->insert([
            'name' => 'Small Bungalow',
        ]);

        DB::table('home_types')->insert([
            'name' => 'Large Bungalow',
        ]);

        DB::table('home_types')->insert([
            'name' => 'Small Condo',
        ]);

        DB::table('home_types')->insert([
            'name' => 'Large Condo',
        ]);

        DB::table('home_types')->insert([
            'name' => 'Small 2-Story',
        ]);

        DB::table('home_types')->insert([
            'name' => 'Large 2-Story',
        ]);

        DB::table('home_types')->insert([
            'name' => 'Larger Home',
        ]);
    }
}
