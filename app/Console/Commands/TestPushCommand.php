<?php

namespace Custodia\Console\Commands;

use Custodia\User;
use Illuminate\Console\Command;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;



class TestPushCommand extends Command
{
    protected $signature = "test:push";
    protected $description = "test push notification";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fcm = app('FirebaseService')->getMessaging();

        $user = User::where("email", "chris@custodia.com")->first();

        $tokens = $user->tokens()->where('scope', 'fcm_token')->pluck('token')->toArray();

        $title = 'My Notification Title';
        $body = 'My Notification Body';

        $notification = Notification::create($title, $body);

        $message = CloudMessage::new()
                               ->withNotification($notification);

        $ret = $fcm->sendMulticast($message, $tokens);
        print_r($ret);
    }
}