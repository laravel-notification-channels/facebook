<?php

namespace NotificationChannels\Facebook;

use Illuminate\Notifications\Notification;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;

class FacebookChannel
{
    /** @var Facebook */
    private $fb;

    /**
     * FacebookChannel constructor.
     *
     * @param Facebook $fb
     */
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
     * @throws \NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFacebook($notifiable);

        if (is_string($message)) {
            $message = FacebookMessage::create($message);
        }

        if ($message->toNotGiven()) {
            if (! $to = $notifiable->routeNotificationFor('facebook')) {
                throw CouldNotCreateMessage::recipientNotProvided();
            }

            $message->to($to);
        }

        if ($message->senderGiven()) {
            $this->fb = new Facebook($message->sender);
        }

        $this->fb->send($message->toArray());
    }
}
