<?php

namespace Custodia\Console\Commands;

use Custodia\Services\UserNotificationService;
use Custodia\Services\UserService;
use Custodia\Services\WeatherTriggerService;
use Custodia\User;
use Illuminate\Console\Command;

class WeeklyNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:weekly_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly notification to users.  Scheduled for Monday morning.';

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserNotificationService
     */
    private $userNotificationService;

    /**
     * @var WeatherTriggerService $weatherTriggerService
     */
    var $weatherTriggerService;

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
        $users = User::with('tokens')
                     ->has('tokens')
                     ->whereHas('tokens', function($query) {
                         $query->where('scope', '=', 'fcm_token');
                     })->get();

        foreach ($users as $user) {
            $user_points = 0;
            $user_items = 0;

            $items = $this->userService->getAllMaintenanceItemsTodayByUser($user, $this->weatherTriggerService);

            foreach ($items as $item) {
                $user_items++;
                $user_points += $item['points'];
            }

            $user_score = $this->userService->getScoreByUser($user);
            $user_potential = $this->userService->getPotentialScoreByUser($user);
            $week_potential = (int)(($user_points / $user_potential) * 100.0);

            if ($user_score < 50) {
                $score_message = "needs improvement";
            } else if ($user_score < 80) {
                $score_message = "could be better";
            } else {
                $score_message = "is great, keep it up";
            }

            if ($week_potential > 0) {
                $week_message = "There are at least $user_items things you can do to increase it by $week_potential% this week.";
            } else {
                // TODO determine what to put here, if anything
                $week_message = '';
            }

            $title = "This Week In Home Management";
            $body = "Your HMP score $score_message!";

            if (!empty($week_message))
                $body .= "  $week_message";

            $this->userNotificationService->sendNotificationToUser($user, $title, $body);
        }
    }
}