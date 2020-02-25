<?php

namespace Custodia\Services;

use Custodia\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification;

class UserNotificationService
{
    /**
     * @param User   $user
     * @param string $title
     * @param string $body
     * @return void
     */
    public function sendNotificationToUser(User $user, string $title, string $body) {
        $tokens = $user->tokens()->where('scope', 'fcm_token')->pluck('token')->toArray();

        if (empty($tokens))
            return; // no notification can be sent

        Log::debug("sending notification", [ $user->email, $title, $body ]);
        $notification = Notification::create($title, $body);

        $message = CloudMessage::new()
                               ->withNotification($notification);

        $fcm = app('FirebaseService')->getMessaging();

        /** @var MulticastSendReport|null $report */
        $report = $fcm->sendMulticast($message, $tokens);

        if ($report->hasFailures()) {
            foreach ($report->failures()->getItems() as $failure) {
                Log::warning("failed to send notification to device", [
                    'target' => $failure->target()->value(),
                    'error' => $failure->error()->getMessage()
                ]);
            }
        }
    }
}