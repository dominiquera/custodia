<?php

namespace Custodia\Console\Commands;

use Custodia\Job;
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
        $job = Job::where('name', '=', 'WeeklyScoring')->firstOrFail();
        $last_execution_date = $job->last_execution_date;
        $job->last_execution_date = date("Y-m-d H:i:s");
        $job->save();

        $month = date('F');

        $monthlyEvents = MonthlyEvent::where('month', '=', $month)->get();

        echo "Running Weekly Scoring command..." . PHP_EOL . PHP_EOL;
        echo $this->calcDaysSinceDate($last_execution_date) . " days since last execution. (" . $last_execution_date . ")" . PHP_EOL . PHP_EOL;
        echo "Month: " . $month . PHP_EOL . PHP_EOL;

        echo "Monthly Events: " . PHP_EOL;
        foreach ($monthlyEvents as $monthlyEvent){
            echo $monthlyEvent->title . PHP_EOL;
        }
        echo PHP_EOL;


        $users = User::where('role_id', '=', Role::where('name', '=', 'User')->firstOrFail()->id)->get();
        foreach ($users as $user){
            echo "Processing User: " . $user->id . PHP_EOL;
            $userProfile = $user->userProfile;
            foreach ($monthlyEvents as $monthlyEvent){
                echo "Processing Monthly Event: " . $monthlyEvent->title . PHP_EOL;

                foreach ($monthlyEvent->maintenanceItems as $maintenanceItem){
                    echo "Processing Maintenance Item: " . $maintenanceItem->title . PHP_EOL;

                    if ($maintenanceItem->homeTypes->contains($user->userProfile->homeType)){
                        if (!$user->ignoredMaintenanceItems->contains($maintenanceItem)){
                            if ($this->hasMatchingOutdoorSpace($user, $maintenanceItem)){
                                if ($this->hasMatchingDrivewayType($user, $maintenanceItem)){
                                    if ($this->hasMatchingHomeFeature($user, $maintenanceItem)){
                                        if ($this->hasMatchingMobilityIssue($user, $maintenanceItem)){
                                            $numTimesMissed = $this->calcNumTimesMaintenanceItemMissedSinceLastRun($user,$maintenanceItem, $last_execution_date);
                                            if ($numTimesMissed > 0){
                                                $pointsToSubtract = $maintenanceItem->points * $numTimesMissed;
                                                echo "User missed item " . $numTimesMissed . " times. ";
                                                echo "Subtracting " . $pointsToSubtract . " points" . PHP_EOL;
                                                $userProfile->score = $userProfile->score - $pointsToSubtract;
                                                $userProfile->save();
                                            }
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


    private function calcNumTimesMaintenanceItemMissedSinceLastRun(User $user, MaintenanceItem $maintenanceItem, $lastExecutionDate){
        //calculate how many times the item has been missed since last run
        //ie if daily, we should have had 7 this week. Calculate how many are expected minus how many are done.

        //@TODO WE NEED TO CONSIDER WHEN THE USER REGISTERED. CANT GIVE THEM -1000 ON FIRST WEEK.

        $interval = $maintenanceItem->interval;

        if ($interval->name == "Daily"){
            //item should have been done once for each day since last execution
            $itemsDone = $user->doneMaintenanceItems()
                ->where('maintenance_item_id', '=', $maintenanceItem->id)
                ->where('maintenance_item_done_user.created_at', '>', $lastExecutionDate)->get();

            $daysSinceLastExecution = $this->calcDaysSinceDate($lastExecutionDate);

            $numMissed = max($daysSinceLastExecution - sizeof($itemsDone), 0);
            return $numMissed;
        }
        if ($interval->name == "Weekly"){
            //item should be done once in the last week
            $itemsDone = $user->doneMaintenanceItems()
                ->where('maintenance_item_id', '=', $maintenanceItem->id)
                ->where('maintenance_item_done_user.created_at', '>', date('Y-m-d H:i:s', strtotime("-7 day")))->get();

            $numMissed = max(1 - sizeof($itemsDone), 0);
            return $numMissed;
        }
        if ($interval->name == "Biweekly"){
            //item should be done once in the last 14 days

            //get items done in last 14 days
            $itemsDone = $user->doneMaintenanceItems()
                ->where('maintenance_item_id', '=', $maintenanceItem->id)
                ->where('maintenance_item_done_user.created_at', '>', date('Y-m-d H:i:s', strtotime("-14 day")))->get();

            $numMissed = max(1 - sizeof($itemsDone), 0);
            return $numMissed;
        }
        if ($interval->name == "Monthly"){
            //if this is the first time we run the script this month, check if they did it at all last month.
            //so if first time we run it in feb, check the user did it at all in january.
            $lastExecutionMonth = date('F', strtotime($lastExecutionDate));
            $lastExecutionYear = date('Y', strtotime($lastExecutionDate));

            $currentMonth = date('F');

            if ($currentMonth != $lastExecutionMonth){
                $lastExecutionMonthNum = date('n', strtotime($lastExecutionDate));

                //get all items done last month
                $itemsDone = $user->doneMaintenanceItems()
                    ->where('maintenance_item_id', '=', $maintenanceItem->id)
                    ->where( DB::raw('MONTH(maintenance_item_done_user.created_at)'), '=', $lastExecutionMonthNum)
                    ->where( DB::raw('YEAR(maintenance_item_done_user.created_at)'), '=', $lastExecutionYear)->get();

                $numMissed = max(1 - sizeof($itemsDone), 0);
                return $numMissed;
            }
        }

        return 0;
    }

    private function calcDaysSinceDate(String $date){
        $now = time();
        $datediff = $now - strtotime($date);
        return round($datediff / (60 * 60 * 24));
    }

    private function roundUp($number)
    {
        $fig = (int) str_pad('1', 0, '0');
        return (int) (ceil($number * $fig) / $fig);
    }

}
