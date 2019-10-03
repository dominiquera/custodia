<?php

use Illuminate\Database\Seeder;

class DrivewayTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('driveway_types')->insert([
            'name' => 'No Driveway',
        ]);
        DB::table('driveway_types')->insert([
            'name' => '2-Car Driveway',
        ]);
        DB::table('driveway_types')->insert([
            'name' => '4-Car Driveway',
        ]);
        DB::table('driveway_types')->insert([
            'name' => '6-Car Driveway',
        ]);
        DB::table('driveway_types')->insert([
            'name' => '8-Car Driveway',
        ]);
        DB::table('driveway_types')->insert([
            'name' => '10-Car Driveway',
        ]);
    }
}
