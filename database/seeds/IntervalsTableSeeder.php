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
            "Daily",
            "Weekly",
            "Biweekly",
            "Monthly",
            "Bimonthly",
            "Quarterly",
            "Annually",
            "Biannually",
            "Triannually",
            "Weather Trigger",
            "One Time"
        ];

        foreach ($intervals as $interval){
            DB::table('intervals')->insert([
                'name' => $interval,
            ]);
        }
    }
}
