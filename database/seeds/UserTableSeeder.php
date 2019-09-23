<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'dorademacher@gmail.com',
            'role_id' => 1,
            'password' => bcrypt('secret'),
        ]);
        DB::table('users')->insert([
            'name' => 'Dean Hopkins',
            'email' => 'deanhopkins@gmail.com',
            'role_id' => 2,
            'password' => bcrypt('secret'),
        ]);
    }
}
