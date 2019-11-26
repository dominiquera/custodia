<?php

use Illuminate\Database\Seeder;

class IntervalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $intervals = [
//            "Daily",
            "Weekly",
            "Biweekly",
            "Monthly"
//            "Weather Trigger"
        ];

        foreach ($intervals as $interval){
            DB::table('intervals')->insert([
                'name' => $interval,
            ]);
        }
    }
}
