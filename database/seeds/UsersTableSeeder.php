<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->create_admin_user();
        $this->create_test_user();
    }

    private function create_admin_user(){
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'dorademacher@gmail.com',
            'role_id' => 1,
            'password' => bcrypt('secret'),
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => 1,
            'home_type_id' => 1,
        ]);
    }

    private function create_test_user(): void
    {
        DB::table('users')->insert([
            'name' => 'Test User',
            'email' => 'custodiatest@mailinator.com',
            'role_id' => 2,
            'password' => bcrypt('secret'),
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => 2,
            'home_type_id' => 2,
        ]);

        DB::table('maintenance_item_done_user')->insert([
            'user_id' => 2,
            'maintenance_item_id' => 1,
        ]);

        DB::table('maintenance_item_done_user')->insert([
            'user_id' => 2,
            'maintenance_item_id' => 2,
        ]);

        DB::table('maintenance_item_ignored_user')->insert([
            'user_id' => 2,
            'maintenance_item_id' => 3,
        ]);
    }
}
