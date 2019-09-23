<?php

use Illuminate\Database\Seeder;

class SectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sections')->insert([
            'name' => 'Section One',
        ]);
        DB::table('sections')->insert([
            'name' => 'Section Two',
        ]);
        DB::table('sections')->insert([
            'name' => 'Section Three',
        ]);
    }
}
