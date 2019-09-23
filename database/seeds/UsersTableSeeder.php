<?php

use Illuminate\Database\Seeder;
use Barebone\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->create_static_data();
        $this->createFakeUser1();
        $this->createFakeUser2();
    }

    function create_static_data()
    {
        DB::table('users')->insert([
            'name' => "dominique_rademacher",
            'email' => "dorademacher" . '@gmail.com',
            'password' => bcrypt('secret'),
            'role' => 'admin'
        ]);
    }

    function createFakeUser1()
    {
        $faker = Faker\Factory::create();

        $id = User::create([
            'name' => "Test Case User 1",
            'email' => $faker->email,
            'password' => $faker->password,
            'role' => 'member',
        ])->id;
    }


    function createFakeUser2()
    {
        $faker = Faker\Factory::create();

        $id = User::create([
            'name' => "Test Case User 2",
            'email' => $faker->email,
            'password' => $faker->password,
            'role' => 'member',
        ])->id;
    }
}