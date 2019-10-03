<?php

use Illuminate\Database\Seeder;

class MobilityIssueTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mobility_issue_types')->insert([
            'name' => 'No Mobility Issues',
        ]);
        DB::table('mobility_issue_types')->insert([
            'name' => 'Mild Issues',
        ]);
        DB::table('mobility_issue_types')->insert([
            'name' => 'Some difficulty',
        ]);
        DB::table('mobility_issue_types')->insert([
            'name' => 'Severe difficulty',
        ]);
        DB::table('mobility_issue_types')->insert([
            'name' => 'Wheel chair',
        ]);
    }
}
