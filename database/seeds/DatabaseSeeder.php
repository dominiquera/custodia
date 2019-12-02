<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(IntervalsTableSeeder::class);
        $this->call(HomeTypeSeeder::class);
        $this->call(DrivewayTypeTableSeeder::class);
        $this->call(HomeFeatureTableSeeder::class);
        $this->call(MobilityIssueTypeTableSeeder::class);
        $this->call(OutdoorSpaceTypesTableSeeder::class);
        $this->call(WeatherTriggerTypesTableSeeder::class);
        $this->call(MonthlyEventsTableSeeder::class);
        $this->call(EventTypeSeeder::class);
        $this->call(SectionTableSeeder::class);
        // $this->call(MaintenanceItemSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(JobsTableSeeder::class);
        $this->call(MonthSeeder::class);
        $this->call(ManagementPlanTable::class);
    }
}
