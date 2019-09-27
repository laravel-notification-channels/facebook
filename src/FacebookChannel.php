<?php

namespace NotificationChannels\Facebook;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use NotificationChannels\Facebook\Exceptions\{CouldNotCreateMessage, CouldNotSendNotification};

/**
 * Class FacebookChannel
 */
class FacebookChannel
{
    /** @var Facebook */
    private $fb;

    /**
     * FacebookChannel constructor.
     *
     * @param  Facebook  $fb
     */
    public function __construct(Facebook $fb)
    {
        $this->fb = $fb;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed         $notifiable
     * @param  Notification  $notification
     *
     * @throws CouldNotCreateMessage
     * @throws CouldNotSendNotification
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification): void
    {
        $message = $notification->toFacebook($notifiable);

        if (is_string($message)) {
            $message = FacebookMessage::create($message);
        }

        if ($message->toNotGiven()) {
            if (!$to = $notifiable->routeNotificationFor('facebook')) {
                throw CouldNotCreateMessage::recipientNotProvided();
            }

            $message->to($to);
        }

        $this->fb->send($message->toArray());
    }
}
