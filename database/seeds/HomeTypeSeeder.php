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
            'name' => 'Type 1',
        ]);

        DB::table('home_types')->insert([
            'name' => 'Type 2',
        ]);
    }
}
