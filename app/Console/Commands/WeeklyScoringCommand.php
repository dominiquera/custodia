<?php

namespace Custodia\Console\Commands;

use Custodia\Job;
use Custodia\MaintenanceItem;
// use Custodia\MonthlyEvent;
use Custodia\Month;
use Custodia\OutdoorSpaceType;
use Custodia\Role;
use Custodia\User;
use Illuminate\Console\Command;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

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
        if ($last_execution_date == null) {
          $last_execution_date = date("Y-m-d", strtotime('-7 days'));
        }
        $job->last_execution_date = date("Y-m-d H:i:s");
        $job->save();

        $month = date('F');

        $months = Month::where('month', '=', $month)->get();
        // TODO: this part has to change here

        echo "Running Weekly Scoring command..." . PHP_EOL . PHP_EOL;
        echo $this->calcDaysSinceDate($last_execution_date) . " days since last execution. (" . $last_execution_date . ")" . PHP_EOL . PHP_EOL;
        echo "Month: " . $month . PHP_EOL . PHP_EOL;

        // echo "Monthly Events: " . PHP_EOL;
        // foreach ($monthlyEvents as $monthlyEvent){
        //     echo $monthlyEvent->title . PHP_EOL;
        // }
        // echo PHP_EOL;

        $allMaintenanceItems = MaintenanceItem::all();


        $users = User::where('role_id', '=', Role::where('name', '=', 'User')->firstOrFail()->id)->get();
        $plucked = $months->pluck('maintenance_item_id')->all();
        foreach ($users as $user) {
            echo "Processing User: " . $user->id . PHP_EOL;
            $userProfile = $user->userProfile;
            // foreach ($months as $m){
                // echo "Processing Month: " . $m->month . PHP_EOL;


                // var_dump($plucked);exi



                foreach ($allMaintenanceItems as $maintenanceItem){

                    if (in_array($maintenanceItem->id,$plucked)) {
                    echo "Processing Maintenance Item: " . $maintenanceItem->title . PHP_EOL;
                    if ($maintenanceItem->homeTypes->contains($user->userProfile->homeType)) {
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


                                                $token = $user->firebase_registration_token;

                                                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

                                                $notification = [
                                                    'title' => $title,
                                                    'sound' => true,
                                                ];

                                                $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

                                                $fcmNotification = [
                                                    //'registration_ids' => $tokenList, //multple token array
                                                    'to'        => $token, //single token
                                                    'notification' => $notification,
                                                    'data' => $extraNotificationData
                                                ];

                                                $headers = [
                                                    'Authorization: key=AIzaSyBHmgFKeuw05iu12qvJfDgP5hVAQo5h80w',
                                                    'Content-Type: application/json'
                                                ];


                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                                                curl_setopt($ch, CURLOPT_POST, true);
                                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                                                $result = curl_exec($ch);
                                                curl_close($ch);



                                            } else {
                                              echo "Ignoring because: Number of missed times was not greater than zero". PHP_EOL;
                                            }
                                        } else {
                                            echo "Ignoring because: No relevant mobility issue type". PHP_EOL;
                                        }
                                    } else {
                                        echo "Ignoring because: No relevant home features". PHP_EOL;
                                    }
                                } else {
                                    echo "Ignoring because: No relevant driveway types". PHP_EOL;
                                }
                            } else {
                                echo "Ignoring because: No relevant outdoor spaces". PHP_EOL;
                            }
                        } else {
                            echo "Ignoring because: Maintenance Item ignored by User". PHP_EOL;
                        }
                    } else {
                        echo "Ignoring because: Home Type incompatible". PHP_EOL;
                    }
                  } else {
                    //echo "Ignoring because: Maintenance Item not within this month";
                  }
                    // echo PHP_EOL;
                }
            //     echo PHP_EOL;
            // }
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




        $interval = $maintenanceItem->months->first(function ($value, $key) {
            $m = date('F');
            return $value->month == $m;
        });

        $interval = $interval->interval;



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
