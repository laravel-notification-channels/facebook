<?php

namespace NotificationChannels\Facebook;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;
use NotificationChannels\Facebook\Exceptions\CouldNotSendNotification;

/**
 * Class FacebookChannel.
 */
class FacebookChannel
{
    /** @var Facebook */
    private $fb;

    /**
     * FacebookChannel constructor.
     */
    public function __construct(Facebook $fb)
    {
        $this->fb = $fb;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     *
     * @throws CouldNotCreateMessage
     * @throws CouldNotSendNotification
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification): array
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

        $response = $this->fb->send($message->toArray());

        return json_decode($response->getBody()->getContents(), true);
    }
}
