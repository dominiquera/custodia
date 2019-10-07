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

        $this->import_maintenance_items_from_csv();
    }

    private function import_maintenance_items_from_csv(){
        // Open the file for reading
        if (($h = fopen("database/seeds/resources/custodia_db.csv", "r")) !== FALSE) {
            // Convert each line into the local $data variable
            $csv_city = "";
            $csv_province = "";

            $count = 0;
            while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
                if ($count > 1){ //ignore title headers
                    $csv_item_name = $data[1];
                    $csv_points = $data[2];
                    $csv_interval = $data[3];

                    $csv_jan = $data[4];
                    $csv_feb = $data[5];
                    $csv_mar = $data[6];
                    $csv_apr = $data[7];
                    $csv_may = $data[8];
                    $csv_jun = $data[9];
                    $csv_jul = $data[10];
                    $csv_aug = $data[11];
                    $csv_sep = $data[12];
                    $csv_oct = $data[13];
                    $csv_nov = $data[14];
                    $csv_dec = $data[15];

                    $csv_mobility_priority = $data[16];
                    $csv_weather_trigger_name = $data[17];
                    $csv_newsfeed_section_name = $data[18];

                    $csv_home_type_small_bungalow = $data[25];
                    $csv_home_type_large_bungalow = $data[26];
                    $csv_home_type_small_condo = $data[27];
                    $csv_home_type_large_condo = $data[28];
                    $csv_home_type_small_two_story = $data[29];
                    $csv_home_type_large_two_story = $data[30];
                    $csv_home_type_larger_home = $data[31];
                    $csv_home_type_all = $data[32];

                    $csv_outdoor_space_small_yard = $data[33];
                    $csv_outdoor_space_medium_yard = $data[34];
                    $csv_outdoor_space_large_yard = $data[35];
                    $csv_outdoor_space_gardens = $data[36];
                    $csv_outdoor_space_hedges = $data[37];
                    $csv_outdoor_space_a_few_trees = $data[38];
                    $csv_outdoor_space_lots_of_trees = $data[39];
                    $csv_outdoor_space_all = $data[40];

                    $csv_driveway_none = $data[41];
                    $csv_two_car_driveway = $data[42];
                    $csv_four_car_driveway = $data[43];
                    $csv_six_car_driveway = $data[44];
                    $csv_eight_car_driveway = $data[45];
                    $csv_ten_car_driveway = $data[46];
                    $csv_driveway_all = $data[47];

                    $csv_home_feature_deck = $data[48];
                    $csv_home_feature_porch = $data[49];
                    $csv_home_feature_balcony = $data[50];
                    $csv_home_feature_walkway = $data[51];
                    $csv_home_feature_fireplace = $data[52];
                    $csv_home_feature_pool = $data[53];
                    $csv_home_feature_all = $data[54];

                    $csv_mob_issues_none = $data[55];
                    $csv_mob_issues_mild = $data[56];
                    $csv_mob_issues_some = $data[57];
                    $csv_mob_issues_sever = $data[58];
                    $csv_mob_issues_wheelchair = $data[59];
                    $csv_mob_issues_all = $data[60];

                    $csv_summary = $data[71];
                    $csv_cautions = $data[78];


                    $interval = \Custodia\Interval::where('name', '=', $csv_interval)->firstOrFail();
                    $section = \Custodia\Section::where('name', '=', $csv_newsfeed_section_name)->first();

                    $item = new \Custodia\MaintenanceItem();
                    $item->title = $csv_item_name;
                    if ($section){
                        $item->section_id = $section->id;
                    }
                    if ((strlen($csv_mobility_priority) > 0) && (strtolower($csv_mobility_priority) != "no")){
                        $item->mobility_priority = true;
                    }
                    $item->interval_id = $interval->id;
                    $item->points = $csv_points;
                    $item->featured_image_id = 1;
                    $item->summary = $csv_summary;
                    $item->cautions = $csv_cautions;
                    $item->save();

                    if ($csv_jan == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'January')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_feb == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'February')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_mar == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'March')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_apr == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'April')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_may == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'May')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_jun == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'June')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_jul == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'July')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_aug == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'August')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_sep == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'September')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_oct == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'October')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_nov == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'November')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if ($csv_dec == "x"){
                        $event = \Custodia\MonthlyEvent::where('month', '=', 'December')->firstOrfail();
                        $event->maintenanceItems()->attach($item);
                    }

                    if (strlen($csv_weather_trigger_name) > 0){
                        $trigger = \Custodia\WeatherTriggerType::where('name', '=', $csv_weather_trigger_name)->first();
                        if (isset($trigger)){
                            $item->weather_trigger_type_id = $trigger->id;
                        }
                    }

                    if ($csv_home_type_all == "x"){
                        foreach (\Custodia\HomeType::all() as $home_type){
                            $item->homeTypes()->attach($home_type);
                        }
                    } else {
                        if ($csv_home_type_small_bungalow == "x"){
                            $home_type = \Custodia\HomeType::where('name', '=', 'Small Bungalow')->firstOrFail();
                            $item->homeTypes()->attach($home_type);
                        }

                        if ($csv_home_type_large_bungalow == "x"){
                            $home_type = \Custodia\HomeType::where('name', '=', 'Large Bungalow')->firstOrFail();
                            $item->homeTypes()->attach($home_type);
                        }

                        if ($csv_home_type_small_condo == "x"){
                            $home_type = \Custodia\HomeType::where('name', '=', 'Small Condo')->firstOrFail();
                            $item->homeTypes()->attach($home_type);
                        }

                        if ($csv_home_type_large_condo == "x"){
                            $home_type = \Custodia\HomeType::where('name', '=', 'Large Condo')->firstOrFail();
                            $item->homeTypes()->attach($home_type);
                        }

                        if ($csv_home_type_small_two_story == "x"){
                            $home_type = \Custodia\HomeType::where('name', '=', 'Small 2-Story')->firstOrFail();
                            $item->homeTypes()->attach($home_type);
                        }

                        if ($csv_home_type_large_two_story == "x"){
                            $home_type = \Custodia\HomeType::where('name', '=', 'Large 2-Story')->firstOrFail();
                            $item->homeTypes()->attach($home_type);
                        }

                        if ($csv_home_type_larger_home == "x"){
                            $home_type = \Custodia\HomeType::where('name', '=', 'Larger Home')->firstOrFail();
                            $item->homeTypes()->attach($home_type);
                        }
                    }





                    if ($csv_outdoor_space_all == "x"){
                        foreach (\Custodia\OutdoorSpaceType::all() as $outdoor_space_type){
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                    } else {
                        if ($csv_outdoor_space_small_yard == "x"){
                            $outdoor_space_type = \Custodia\OutdoorSpaceType::where('name', '=', 'Small Yard')->firstOrFail();
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                        if ($csv_outdoor_space_medium_yard == "x"){
                            $outdoor_space_type = \Custodia\OutdoorSpaceType::where('name', '=', 'Medium Yard')->firstOrFail();
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                        if ($csv_outdoor_space_large_yard == "x"){
                            $outdoor_space_type = \Custodia\OutdoorSpaceType::where('name', '=', 'Large Yard')->firstOrFail();
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                        if ($csv_outdoor_space_gardens == "x"){
                            $outdoor_space_type = \Custodia\OutdoorSpaceType::where('name', '=', 'Gardens')->firstOrFail();
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                        if ($csv_outdoor_space_hedges == "x"){
                            $outdoor_space_type = \Custodia\OutdoorSpaceType::where('name', '=', 'Hedges')->firstOrFail();
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                        if ($csv_outdoor_space_a_few_trees == "x"){
                            $outdoor_space_type = \Custodia\OutdoorSpaceType::where('name', '=', 'A few trees')->firstOrFail();
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                        if ($csv_outdoor_space_lots_of_trees == "x"){
                            $outdoor_space_type = \Custodia\OutdoorSpaceType::where('name', '=', 'Lots of trees')->firstOrFail();
                            $item->outdoorSpaces()->attach($outdoor_space_type);
                        }
                    }




                    if ($csv_driveway_all == "x"){
                        foreach (\Custodia\DrivewayType::all() as $driveway_type){
                            $item->drivewayTypes()->attach($driveway_type);
                        }
                    } else {
                        if ($csv_driveway_none == "x"){
                            $driveway_type = \Custodia\DrivewayType::where('name', '=', 'No Driveway')->firstOrFail();
                            $item->drivewayTypes()->attach($driveway_type);
                        }
                        if ($csv_two_car_driveway == "x"){
                            $driveway_type = \Custodia\DrivewayType::where('name', '=', '2-Car Driveway')->firstOrFail();
                            $item->drivewayTypes()->attach($driveway_type);
                        }
                        if ($csv_four_car_driveway == "x"){
                            $driveway_type = \Custodia\DrivewayType::where('name', '=', '4-Car Driveway')->firstOrFail();
                            $item->drivewayTypes()->attach($driveway_type);
                        }
                        if ($csv_six_car_driveway == "x"){
                            $driveway_type = \Custodia\DrivewayType::where('name', '=', '6-Car Driveway')->firstOrFail();
                            $item->drivewayTypes()->attach($driveway_type);
                        }
                        if ($csv_eight_car_driveway == "x"){
                            $driveway_type = \Custodia\DrivewayType::where('name', '=', '8-Car Driveway')->firstOrFail();
                            $item->drivewayTypes()->attach($driveway_type);
                        }
                        if ($csv_ten_car_driveway == "x"){
                            $driveway_type = \Custodia\DrivewayType::where('name', '=', '10-Car Driveway')->firstOrFail();
                            $item->drivewayTypes()->attach($driveway_type);
                        }
                    }



                    if ($csv_home_feature_all == "x"){
                        foreach (\Custodia\HomeFeature::all() as $feature){
                            $item->homeFeatures()->attach($feature);
                        }
                    } else {
                        if ($csv_home_feature_deck == "x"){
                            $feature = \Custodia\HomeFeature::where('name', '=', 'Deck')->firstOrFail();
                            $item->homeFeatures()->attach($feature);
                        }
                        if ($csv_home_feature_porch == "x"){
                            $feature = \Custodia\HomeFeature::where('name', '=', 'Porch')->firstOrFail();
                            $item->homeFeatures()->attach($feature);
                        }
                        if ($csv_home_feature_balcony == "x"){
                            $feature = \Custodia\HomeFeature::where('name', '=', 'Balcony')->firstOrFail();
                            $item->homeFeatures()->attach($feature);
                        }
                        if ($csv_home_feature_walkway == "x"){
                            $feature = \Custodia\HomeFeature::where('name', '=', 'Walkway')->firstOrFail();
                            $item->homeFeatures()->attach($feature);
                        }
                        if ($csv_home_feature_fireplace == "x"){
                            $feature = \Custodia\HomeFeature::where('name', '=', 'Fireplace')->firstOrFail();
                            $item->homeFeatures()->attach($feature);
                        }
                        if ($csv_home_feature_pool == "x"){
                            $feature = \Custodia\HomeFeature::where('name', '=', 'Pool')->firstOrFail();
                            $item->homeFeatures()->attach($feature);
                        }
                    }



                    if ($csv_mob_issues_all == "x"){
                        foreach (\Custodia\MobilityIssueType::all() as $issue){
                            $item->mobilityIssues()->attach($issue);
                        }
                    } else {
                        if ($csv_mob_issues_none == "x"){
                            $issue = \Custodia\MobilityIssueType::where('name', '=', 'No Mobility Issues')->firstOrFail();
                            $item->mobilityIssues()->attach($issue);
                        }
                        if ($csv_mob_issues_mild == "x"){
                            $issue = \Custodia\MobilityIssueType::where('name', '=', 'Mild Issues')->firstOrFail();
                            $item->mobilityIssues()->attach($issue);
                        }
                        if ($csv_mob_issues_some == "x"){
                            $issue = \Custodia\MobilityIssueType::where('name', '=', 'Some difficulty')->firstOrFail();
                            $item->mobilityIssues()->attach($issue);
                        }
                        if ($csv_mob_issues_sever == "x"){
                            $issue = \Custodia\MobilityIssueType::where('name', '=', 'Severe difficulty')->firstOrFail();
                            $item->mobilityIssues()->attach($issue);
                        }
                        if ($csv_mob_issues_wheelchair == "x"){
                            $issue = \Custodia\MobilityIssueType::where('name', '=', 'Wheel chair')->firstOrFail();
                            $item->mobilityIssues()->attach($issue);
                        }
                    }


                    $item->save();
                }
                $count++;
            }
        }
    }
}
