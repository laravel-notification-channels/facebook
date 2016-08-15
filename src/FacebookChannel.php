<?php

namespace NotificationChannels\Facebook;

use NotificationChannels\Facebook\Events\MessageWasSent;
use NotificationChannels\Facebook\Events\SendingMessage;
use Illuminate\Notifications\Notification;

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
        if (!$this->shouldSendMessage($notifiable, $notification)) {
            return;
        }

        $message = $notification->toFacebook($notifiable);

        if (is_string($message)) {
            $message = FacebookMessage::create($message);
        }

        if ($message->toNotGiven()) {
            if (!$to = $notifiable->routeNotificationFor('facebook')) {
                return;
            }

            $message->to($to);
        }

        $this->fb->send($message->toArray());

        event(new MessageWasSent($notifiable, $notification));
    }

    /**
     * Check if we can send the notification.
     *
     * @param              $notifiable
     * @param Notification $notification
     *
     * @return bool
     */
    protected function shouldSendMessage($notifiable, Notification $notification)
    {
        return event(new SendingMessage($notifiable, $notification), [], true) !== false;
    }
}
