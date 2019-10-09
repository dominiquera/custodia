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
        echo "Running Weekly Scoring command...\n\n";

        $month = date('F');
        echo "Month: " . $month . "\n\n";

        echo "Monthly Events: \n";
        $monthlyEvents = MonthlyEvent::where('month', '=', $month)->get();
        foreach ($monthlyEvents as $monthlyEvent){
            echo $monthlyEvent->title . "\n";
        }
        echo "\n";

        $users = User::where('role_id', '=', Role::where('name', '=', 'User')->firstOrFail()->id)->get();
        foreach ($users as $user){
            echo "User ID: " . $user->id . "\n";

            foreach ($monthlyEvents as $monthlyEvent){
                echo "Processing Monthly Event: " . $monthlyEvent->title . "\n";
                foreach ($monthlyEvent->maintenanceItems as $maintenanceItem){
                    echo "Processing Maintenance Item: " . $maintenanceItem->title . "\n";
                    //@todo check if maintenance item is relative, if its done, ignored, etc here
                }
                echo "\n";
            }

        }
    }
}
