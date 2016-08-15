<?php

namespace NotificationChannels\Facebook;

use NotificationChannels\Facebook\Exceptions\CouldNotSendNotification;

class FacebookMessage
{
    /** @var string Recipient's ID. */
    public $recipient;

    /** @var string Notification Text. */
    public $text;

    /** @var string Notification Type */
    public $notificationType = 'REGULAR';

    /** @var array Call to Action Buttons */
    public $buttons = [];

    /**
     * @param string $text
     *
     * @return static
     */
    public static function create($text = '')
    {
        return new static($text);
    }

    /**
     * Message constructor.
     *
     * @param string $text
     */
    public function __construct($text = '')
    {
        $this->text($text);
    }

    /**
     * Recipient's PSID or Phone Number.
     *
     * The id must be an ID that was retrieved through the
     * Messenger entry points or through the Messenger webhooks.
     *
     * @param $recipient ID of recipient or Phone number of the recipient
     *                   with the format +1(212)555-2368
     *
     * @return $this
     */
    public function to($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Notification text.
     *
     * @param $text
     *
     * @return $this
     */
    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Push notification type.
     *
     * @param string $notificationType Possible values: REGULAR, SILENT_PUSH, NO_PUSH
     *
     * @return $this
     */
    public function notificationType($notificationType = 'REGULAR')
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    /**
     * Add upto 3 call to action buttons.
     *
     * @param array $buttons
     *
     * @return $this
     * @throws CouldNotSendNotification
     */
    public function buttons(array $buttons = [])
    {
        if (count($buttons) > 3) {
            throw CouldNotSendNotification::messageButtonsLimitExceeded();
        }

        $this->buttons = $buttons;

        return $this;
    }

    /**
     * Determine if user id is not given.
     *
     * @return bool
     */
    public function toNotGiven()
    {
        return ! isset($this->recipient);
    }

    /**
     * Returns message payload.
     *
     * @return array
     */
    public function toArray()
    {
        $recipientType = 'id';
        if (starts_with($this->recipient, '+')) {
            $recipientType = 'phone_number';
        }

        $payload = [];
        $payload['recipient'][$recipientType] = $this->recipient;
        $payload['notification_type'] = $this->notificationType;

        if (empty($this->buttons)) {
            $payload['message']['text'] = $this->text;

            return $payload;
        }

        $attachment = [];
        $attachment['type'] = 'template';
        $attachment['payload']['template_type'] = 'button';
        $attachment['payload']['text'] = $this->text;
        $attachment['payload']['buttons'] = $this->buttons;

        $payload['message']['attachment'] = $attachment;

        return $payload;
    }
}
