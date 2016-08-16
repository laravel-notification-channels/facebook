<?php

namespace NotificationChannels\Facebook;

use Illuminate\Notifications\Notification;
use NotificationChannels\Facebook\Exceptions\CouldNotSendNotification;

class FacebookChannel
{
    /**
     * @var Facebook
     */
    private $fb;

    public function __construct(Facebook $fb)
    {
        $this->fb = $fb;
    }

    /**
     * Send the given notification.
     *
     * @param mixed                                  $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Facebook\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFacebook($notifiable);

        if (is_string($message)) {
            $message = FacebookMessage::create($message);
        }

        if ($message->toNotGiven()) {
            if (! $to = $notifiable->routeNotificationFor('facebook')) {
                throw CouldNotSendNotification::recipientNotProvided();
            }

            $message->to($to);
        }

        $this->fb->send($message->toArray());
    }
}
