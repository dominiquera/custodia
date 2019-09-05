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
        $this->call(HomeTypeSeeder::class);
        $this->call(EventTypeSeeder::class);
        $this->call(SectionTableSeeder::class);
        $this->call(MaintenanceItemSeeder::class);
        $this->call(UserTableSeeder::class);
    }
}
