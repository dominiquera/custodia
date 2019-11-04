<?php

use Illuminate\Database\Seeder;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // DB::table('months')->insert([
      //     'month' => "January"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "February"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "March"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "April"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "May"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "June"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "July"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "August"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "September"
      // ]);
      // DB::table('months')->insert([
      //     'month' => "October"
      // ]);
      DB::table('months')->insert([
          'month' => "November",
          'description' => "Plants in the home give the room more life and feels better",
          'maintenance_item_id' => 106
      ]);

      DB::table('months')->insert([
          'month' => "November",
          'description' => "Keep them alive and well and they will keep you happier",
          'maintenance_item_id' => 107
      ]);
      DB::table('months')->insert([
          'month' => "November",
          'description' => "If you have sprinklers, they should be shut down during the colder months.",
          'maintenance_item_id' => 105
      ]);
      DB::table('months')->insert([
          'month' => "November",
          'description' => "Before winter we turn off the exterior water and prepare for cold",
          'maintenance_item_id' => 103
      ]);

      // DB::table('months')->insert([
      //     'month' => "December"
      // ]);
    }
}
