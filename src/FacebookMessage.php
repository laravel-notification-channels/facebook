<?php

namespace NotificationChannels\Facebook;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;
use NotificationChannels\Facebook\Enums\AttachmentType;
use NotificationChannels\Facebook\Enums\NotificationType;
use NotificationChannels\Facebook\Traits\ButtonsTrait;

/**
 * Class FacebookMessage.
 */
class FacebookMessage implements \JsonSerializable
{
    use ButtonsTrait;

    /** @var string Recipient's ID. */
    public $recipient;

    /** @var string Notification Text. */
    public $text;

    /** @var string Notification Type */
    public $notificationType = 'REGULAR';

    /** @var array Generic Template Cards (items) */
    public $cards = [];

    /** @var string Notification Type */
    public $notification_type = NotificationType::REGULAR;

    /** @var string Attachment Type
     * Defaults to File
     */
    public $attachment_type = AttachmentType::FILE;

    /** @var string Attachment URL */
    public $attachment_url;

    /**
     * @var bool
     */
    protected $has_attachment = false;

    /**
     * @var bool
     */
    protected $has_text = false;

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
     * @param string $text
     */
    public function __construct($text = '')
    {
        if ($text != '') {
            $this->text($text);
        }
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
     * @throws CouldNotCreateMessage
     *
     * @return $this
     */
    public function text($text)
    {
        if (! mb_strlen($text) > 320) {
            $this->text = $text;
        } else {
            throw CouldNotCreateMessage::textTooLong();
        }
        $this->has_text = true;

        return $this;
    }

    /**
     * Add Attachment.
     *
     * @param $attachment_type
     * @param $url
     * @throws CouldNotCreateMessage
     *
     * @return $this
     */
    public function attach($attachment_type, $url)
    {
        $attachment_types = [AttachmentType::FILE, AttachmentType::IMAGE, AttachmentType::VIDEO, AttachmentType::AUDIO];
        if (in_array($attachment_type, $attachment_types)) {
            $this->notificationType = $attachment_type;
        } else {
            throw CouldNotCreateMessage::invalidAttachmentType();
        }


        if (isset($url)) {
            $this->attachment_url = $url;
        } else {
            throw CouldNotCreateMessage::urlNotProvided();
        }

        $this->has_attachment = true;

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
     * Add up to 10 cards to be displayed in a carousel.
     *
     * @param array $cards
     *
     * @return $this
     * @throws CouldNotCreateMessage
     */
    public function cards(array $cards = [])
    {
        if (count($cards) > 10) {
            throw CouldNotCreateMessage::messageCardsLimitExceeded();
        }
        $this->cards = $cards;

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
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Returns message payload for JSON conversion.
     * @throws CouldNotCreateMessage
     * @return array
     */
    public function toArray()
    {
        if ($this->has_attachment) {
            return $this->attachmentMessageToArray();
        }
        if ($this->has_text) {
            //check if has buttons
            if (count($this->buttons) > 0) {
                return $this->buttonMessageToArray();
            }

            return $this->textMessageToArray();
        }
        if (count($this->cards) > 0) {
            return $this->genericMessageToArray();
        }
        throw CouldNotCreateMessage::dataNotProvided();
    }

    /**
     * Returns message for simple text message.
     * @return array
     */
    protected function textMessageToArray()
    {
        $message = [];
        $message['recipient'] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['text'] = $this->text;

        return $message;
    }

    /**
     * Returns message for attachment message.
     * @return array
     */
    protected function attachmentMessageToArray()
    {
        $message = [];
        $message['recipient'] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['attachment']['type'] = $this->attachment_type;
        $message['message']['attachment']['payload']['url'] = $this->attachment_url;

        return $message;
    }

    /**
     * Returns message for Generic Template message.
     * @return array
     */
    protected function genericMessageToArray()
    {
        $message = [];
        $message['recipient'] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['attachment']['type'] = 'template';
        $message['message']['attachment']['payload']['template_type'] = 'generic';
        $message['message']['attachment']['payload']['elements'] = $this->cards;

        return $message;
    }

    /**
     * Returns message for Button Template message.
     * @return array
     */
    protected function buttonMessageToArray()
    {
        $message = [];
        $message['recipient'] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['attachment']['type'] = 'template';
        $message['message']['attachment']['payload']['template_type'] = 'button';
        $message['message']['attachment']['payload']['text'] = $this->text;
        $message['message']['attachment']['payload']['buttons'] = $this->buttons;

        return $message;
    }
}
