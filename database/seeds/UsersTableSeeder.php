<?php

use Custodia\DrivewayType;
use Custodia\HomeFeature;
use Custodia\HomeType;
use Custodia\MobilityIssueType;
use Custodia\OutdoorSpaceType;
use Custodia\Role;
use Custodia\User;
use Custodia\UserProfile;
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
        $this->create_test_case_user_one();
        $this->create_test_case_user_two();
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
            'phone' => '07987654321',
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


    private function create_test_case_user_one(): void
    {
        /*
         * Test User: 1
         *
         * Role: User
         * Home Type: Small Bungalow
         * Outdoor Spaces: Small Yard, Hedges
         * Driveway: No Driveway
         * Home Features: Porch, Walkway
         * Mobility Issues: No Mobility Issues
         */
        $user = new User();
        $user->name = "Test Case User One";
        $user->email = 'custodia_test_one@mailinator.com';
        $user->phone = '07123456789';
        $role = Role::where('name', '=', 'User')->firstOrFail();
        $user->role_id = $role->id;
        $user->password = bcrypt('test_user_one');
        $user->save();

        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $home_type = HomeType::where('name', '=', 'Small Bungalow')->firstOrFail();
        $profile->home_type_id = $home_type->id;
        $profile->save();

        $outdoor_space_small_yard = OutdoorSpaceType::where('name', '=', 'Small Yard')->firstOrFail();
        $outdoor_space_hedge = OutdoorSpaceType::where('name', '=', 'Hedges')->firstOrFail();
        $profile->outdoorSpaces()->attach($outdoor_space_small_yard);
        $profile->outdoorSpaces()->attach($outdoor_space_hedge);

        $driveway = DrivewayType::where('name', '=', 'No Driveway')->firstOrFail();
        $profile->drivewayTypes()->attach($driveway);

        $home_features_porch = HomeFeature::where('name', '=', 'Porch')->firstOrFail();
        $home_features_walkway = HomeFeature::where('name', '=', 'Walkway')->firstOrFail();
        $profile->homeFeatures()->attach($home_features_porch);
        $profile->homeFeatures()->attach($home_features_walkway);

        $mob_issue = MobilityIssueType::where('name', '=', 'No Mobility Issues')->firstOrFail();
        $profile->mobilityIssues()->attach($mob_issue);

        $profile->save();
    }


    private function create_test_case_user_two(): void
    {
        /*
         * Test User: 2
         *
         * Role: User
         * Home Type: Large Condo
         * Outdoor Spaces: Small Yard, Medium Yard, Large Yard, Gardens, Hedges, A few trees, Lots of trees
         * Driveway: 4-Car Driveway
         * Home Features: Deck, Porch, Balcony, Walkway, Fireplace, Pool
         * Mobility Issues: Some difficulty, Wheel chair
         */
        $user = new User();
        $user->name = "Test Case User Two";
        $user->email = 'custodia_test_two@mailinator.com';
        $user->phone = '07222222222';
        $role = Role::where('name', '=', 'User')->firstOrFail();
        $user->role_id = $role->id;
        $user->password = bcrypt('test_user_two');
        $user->save();

        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $home_type = HomeType::where('name', '=', 'Large Condo')->firstOrFail();
        $profile->home_type_id = $home_type->id;
        $profile->save();

        $outdoorSpaces = ["Small Yard", "Medium Yard", "Large Yard", "Gardens", "Hedges", "A few trees", "Lots of trees"];
        foreach ($outdoorSpaces as $outdoorSpace){
            $obj = OutdoorSpaceType::where('name', '=', $outdoorSpace)->firstOrFail();
            $profile->outdoorSpaces()->attach($obj);
        }

        $driveway = DrivewayType::where('name', '=', '4-Car Driveway')->firstOrFail();
        $profile->drivewayTypes()->attach($driveway);

        $homeFeatures = ["Deck", "Porch", "Balcony", "Walkway", "Fireplace", "Pool"];
        foreach ($homeFeatures as $homeFeature){
            $obj = HomeFeature::where('name', '=', $homeFeature)->firstOrFail();
            $profile->homeFeatures()->attach($obj);
        }

        $mob_issue = MobilityIssueType::where('name', '=', 'Some difficulty')->firstOrFail();
        $profile->mobilityIssues()->attach($mob_issue);
        $mob_issue = MobilityIssueType::where('name', '=', 'Wheel chair')->firstOrFail();
        $profile->mobilityIssues()->attach($mob_issue);

        $profile->save();
    }
}
