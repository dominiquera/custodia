<?php

use Illuminate\Database\Seeder;

class MaintenanceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('images')->insert([
            'path' => '/storage/images/default_profile.png'
        ]);

        DB::table('maintenance_items')->insert([
            'title' => "Add Freshener To Dishwasher",
            'section_id' => 3,
            'interval_id' => 9,
            'points' => 8,
            'featured_image_id' => 1,
            'summary' => "We have the magic touch when it comes to leaving things fresh.  Custodia will add dishwasher freshener whenever we need to.",
            'cautions' => "On request only"
        ]);

        DB::table('maintenance_items')->insert([
            'title' => "Add Freshener To Refrigerator",
            'section_id' => 3,
            'interval_id' => 9,
            'points' => 10,
            'featured_image_id' => 1,
            'summary' => "Tried and true, a box of baking soda will really freshen things up.  We will use one every 6 months or so to keep your fridge fresh.",
            'cautions' => "On request only"
        ]);

        DB::table('maintenance_items')->insert([
            'title' => "Assisted Mobility Tool Maintenance",
            'section_id' => 2,
            'interval_id' => 6,
            'points' => 10,
            'mobility_priority' => true,
            'featured_image_id' => 1,
            'summary' => "Your Home Service hero is there to help you live a better life in your home and will do their best to help you maintain your mobility devices like walkers and wheelchairs.  We can't fix everything, but we do our best.",
            'cautions' => "Find the stud - you need to find the stud first - if not you need to use a drill and anchors to mount."
        ]);

        DB::table('maintenance_items')->insert([
            'title' => "Careful of the Ice today!",
            'section_id' => 1,
            'interval_id' => 10,
            'points' => 10,
            'mobility_priority' => true,
            'featured_image_id' => 1,
            'summary' => "Your Home Service Hero is there to help in many ways and will never leave your home without sweeping the front walk and steps.  Enjoy this little perk.",
            'cautions' => ""
        ]);

        DB::table('maintenance_items')->insert([
            'title' => "Check driveway/pavement for cracks and fix if possible",
            'section_id' => 1,
            'interval_id' => 7,
            'points' => 10,
            'featured_image_id' => 1,
            'summary' => "Custodia may be able to help maintain your driveway and help it to last longer.  A lot of times, you can catch a crack in the early stages and fill it to keep the moisture out.  That way, it won't expand.",
            'cautions' => ""
        ]);

        DB::table('maintenance_items')->insert([
            'title' => "Check to see your internet as fast as it can be. Restart DSL Modems",
            'section_id' => 2,
            'interval_id' => 8,
            'points' => 4,
            'featured_image_id' => 1,
            'summary' => "Once or twice a year Custodia will restart your internet modem so that it gets a new IP address reboots all of it's firmware.  It will work a little faster for you.",
            'cautions' => ""
        ]);
    }
}
