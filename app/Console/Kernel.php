<?php

namespace Custodia\Console;

use Custodia\Console\Commands\WeatherUpdate;
use Custodia\Console\Commands\WeeklyScoringCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // scoring is now handled dynamically
        // TODO re-introduce as weekly notification for upcoming tasks (how to get points this week + summary of last wk)
        // $schedule->command(WeeklyScoringCommand::class)->weekly();

        // update hourly weather forecasts for customer locations
        $schedule->command(WeatherUpdate::class)->hourly();
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'America/Toronto';
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
