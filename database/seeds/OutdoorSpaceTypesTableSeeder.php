<?php

use Illuminate\Database\Seeder;

class OutdoorSpaceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('outdoor_space_types')->insert([
            'name' => 'No Yard',
        ]);
        DB::table('outdoor_space_types')->insert([
            'name' => 'Small Yard',
        ]);
        DB::table('outdoor_space_types')->insert([
            'name' => 'Medium Yard',
        ]);
        DB::table('outdoor_space_types')->insert([
            'name' => 'Large Yard',
        ]);
        DB::table('outdoor_space_types')->insert([
            'name' => 'Gardens',
        ]);
        DB::table('outdoor_space_types')->insert([
            'name' => 'Hedges',
        ]);
        DB::table('outdoor_space_types')->insert([
            'name' => 'A few trees',
        ]);
        DB::table('outdoor_space_types')->insert([
            'name' => 'Lots of trees',
        ]);
    }
}
