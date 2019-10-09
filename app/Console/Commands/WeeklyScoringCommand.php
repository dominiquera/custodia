<?php

namespace Custodia\Console\Commands;

use Custodia\MonthlyEvent;
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
                            //@todo keep drilling
                            //@todo: Check outdoor spaces, features etc. Make sure its relevant
                            //@todo: If its relevant, check if its been done (Add points) or missed (subtract points)
                        } else {
                            echo "Ignoring because: Maintenance Item ignored by User";
                        }
                    } else {
                        echo "Ignoring because: Home Type incompatible" . PHP_EOL;
                    }
                    echo PHP_EOL;
                }

                echo PHP_EOL;
            }

        }
    }
}
