<?php

namespace Custodia\Console\Commands;

use Custodia\MaintenanceItem;
use Custodia\MonthlyEvent;
use Custodia\OutdoorSpaceType;
use Custodia\Role;
use Custodia\User;
use Illuminate\Console\Command;

class WeeklyScoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:weekly_scoring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate user scores. Scheduled for weekly run.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $month = date('F');
        $monthlyEvents = MonthlyEvent::where('month', '=', $month)->get();

        echo "Running Weekly Scoring command..." . PHP_EOL . PHP_EOL;
        echo "Month: " . $month . PHP_EOL . PHP_EOL;

        echo "Monthly Events: " . PHP_EOL;
        foreach ($monthlyEvents as $monthlyEvent){
            echo $monthlyEvent->title . PHP_EOL;
        }
        echo PHP_EOL;


        $users = User::where('role_id', '=', Role::where('name', '=', 'User')->firstOrFail()->id)->get();
        foreach ($users as $user){
            echo "Processing User: " . $user->id . PHP_EOL;

            foreach ($monthlyEvents as $monthlyEvent){
                echo "Processing Monthly Event: " . $monthlyEvent->title . PHP_EOL;

                foreach ($monthlyEvent->maintenanceItems as $maintenanceItem){
                    echo "Processing Maintenance Item: " . $maintenanceItem->title . PHP_EOL;

                    //@todo check if maintenance item is relevant, if its done, ignored, etc here
                    if ($maintenanceItem->homeTypes->contains($user->userProfile->homeType)){
                        if (!$user->ignoredMaintenanceItems->contains($maintenanceItem)){
                            if ($this->hasMatchingOutdoorSpace($user, $maintenanceItem)){
                                if ($this->hasMatchingDrivewayType($user, $maintenanceItem)){
                                    if ($this->hasMatchingHomeFeature($user, $maintenanceItem)){
                                        if ($this->hasMatchingMobilityIssue($user, $maintenanceItem)){

                                            //@todo check if its done or missed
                                            //@todo add or subtract points etc here
                                        } else {
                                            echo "Ignoring because: No relevant mobility issue type";
                                        }
                                    } else {
                                        echo "Ignoring because: No relevant home features";
                                    }
                                } else {
                                    echo "Ignoring because: No relevant driveway types";
                                }
                            } else {
                                echo "Ignoring because: No relevant outdoor spaces";
                            }
                        } else {
                            echo "Ignoring because: Maintenance Item ignored by User";
                        }
                    } else {
                        echo "Ignoring because: Home Type incompatible";
                    }
                    echo PHP_EOL;
                }

                echo PHP_EOL;
            }

        }
    }

    private function hasMatchingOutdoorSpace(User $user, MaintenanceItem $maintenanceItem){
        foreach ($user->userProfile->outdoorSpaces as $outdoorSpace){
            if ($maintenanceItem->outdoorSpaces->contains($outdoorSpace)){
                return true;
            }
        }
        return false;
    }

    private function hasMatchingDrivewayType(User $user, MaintenanceItem $maintenanceItem){
        foreach ($user->userProfile->drivewayTypes as $drivewayType){
            if ($maintenanceItem->drivewayTypes->contains($drivewayType)){
                return true;
            }
        }
        return false;
    }

    private function hasMatchingHomeFeature(User $user, MaintenanceItem $maintenanceItem){
        foreach ($user->userProfile->homeFeatures as $homeFeature){
            if ($maintenanceItem->homeFeatures->contains($homeFeature)){
                return true;
            }
        }
        return false;
    }

    private function hasMatchingMobilityIssue(User $user, MaintenanceItem $maintenanceItem){
        foreach ($user->userProfile->mobilityIssues as $issue){
            if ($maintenanceItem->mobilityIssues->contains($issue)){
                return true;
            }
        }
        return false;
    }

    private function isMaintenanceItemFinished(){
        //calculate if the item is finished (been done as many times as interval requires)
    }

    private function calcNumTimesMissed($maintenanceItem){
        //calculate how many times the item has been missed this week.
        //ie if daily, we should have had 7 this week.
        //if only done 5 times this week, missed twice.
    }
}
