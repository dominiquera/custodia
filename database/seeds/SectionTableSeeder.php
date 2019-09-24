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
            'name' => 'Outside This Month',
        ]);
        DB::table('sections')->insert([
            'name' => 'Inside This Month',
        ]);
        DB::table('sections')->insert([
            'name' => 'Clean This Month',
        ]);
        DB::table('sections')->insert([
            'name' => 'Focus On Stories',
        ]);
        DB::table('sections')->insert([
            'name' => 'A Focus on Care',
        ]);
        DB::table('sections')->insert([
            'name' => 'Meaningful Products',
        ]);
        DB::table('sections')->insert([
            'name' => 'Related Services',
        ]);
        DB::table('sections')->insert([
            'name' => 'Prevent This Month',
        ]);
    }
}
