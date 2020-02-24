<?php

namespace Custodia\Console\Commands;

use Custodia\Services\UserNotificationService;
use Custodia\Services\UserService;
use Custodia\Services\WeatherTriggerService;
use Custodia\User;
use Illuminate\Console\Command;

class DailyNotificationCommand extends Command
{
    /**
     * @var UserService userService
     */
    var $userService;

    /**
     * @var UserNotificationService userService
     */
    var $userNotificationService;

    /**
     * @var WeatherTriggerService $weatherTriggerService
     */
    var $weatherTriggerService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:daily_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send trigger notifications to users. Scheduled for daily run.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserService $userService,
                                UserNotificationService $userNotificationService,
                                WeatherTriggerService $weatherTriggerService)
    {
        parent::__construct();
        $this->userService = $userService;
        $this->userNotificationService = $userNotificationService;
        $this->weatherTriggerService = $weatherTriggerService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = $this->userService->getUsersWithLocations();

        foreach ($users as $user) {
            // skip users without FCM tokens
            if ($user->tokens()->count() == 0)
                continue;

            $triggered_items = $this->userService->getTriggeredMaintenanceItemsTodayByUser($user, $this->weatherTriggerService);

            // TODO batch notifications of same title/body (supports to up to 500 devices at once)
            // TODO should cycle through available notification texts in a series (notification N uses text N)
            foreach ($triggered_items as $triggered_item) {
                if ($triggered_item['months']['interval'] == "Weather Trigger")
                    $title = 'Weather';
                else
                    $title = 'Maintain';

                $this->userNotificationService->sendNotificationToUser($user,
                                                                       "Reminder",
                                                                       $triggered_item['months']['description']);
            }
        }
    }
}