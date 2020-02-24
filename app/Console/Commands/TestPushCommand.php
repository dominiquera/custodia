<?php

namespace Custodia\Console\Commands;

use Custodia\Services\UserNotificationService;
use Custodia\User;
use Illuminate\Console\Command;

class TestPushCommand extends Command
{
    protected $signature = "test:push";
    protected $description = "test push notification";
    /**
     * @var UserNotificationService $userNotificationService
     */
    var $userNotificationService;

    public function __construct(UserNotificationService $userNotificationService) {
        parent::__construct();
        $this->userNotificationService = $userNotificationService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where("email", "chris@custodia.com")->first();

        $title = 'Test at ' . date("Y-m-d H:i:s");
        $body = 'Just testing, click or swipe.';

        $this->userNotificationService->sendNotificationToUser($user, $title, $body);
    }
}