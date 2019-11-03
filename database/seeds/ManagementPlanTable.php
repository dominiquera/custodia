<?php

use Illuminate\Database\Seeder;

class ManagementPlanTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
      DB::table('management_plans')->insert([
          'name' => 'Mom',
      ]);
      DB::table('management_plans')->insert([
          'name' => 'Dad',
      ]);
      DB::table('management_plans')->insert([
          'name' => 'Me',
      ]);
      DB::table('management_plans')->insert([
          'name' => 'Other',
      ]);

    }
}
