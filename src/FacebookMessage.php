<?php

namespace NotificationChannels\Facebook;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;
use NotificationChannels\Facebook\Enums\AttachmentType;
use NotificationChannels\Facebook\Enums\NotificationType;
use NotificationChannels\Facebook\Traits\HasButtons;

/**
 * Class FacebookMessage.
 */
class FacebookMessage implements \JsonSerializable
{
    use HasButtons;

    /** @var string Recipient's ID. */
    public $recipient;

    /** @var string Notification Text. */
    public $text;

    /** @var string Notification Type */
    public $notificationType = NotificationType::REGULAR;

    /** @var array Generic Template Cards (items) */
    public $cards = [];

    /** @var string Attachment Type. Defaults to File */
    public $attachmentType = AttachmentType::FILE;

    /** @var string Attachment URL */
    public $attachmentUrl;

    /** @var bool */
    protected $hasAttachment = false;

    /** @var bool */
    protected $hasText = false;

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
     *
     * @throws CouldNotCreateMessage
     *
     * @return $this
     */
    public function text($text)
    {
        if (mb_strlen($text) > 320) {
            throw CouldNotCreateMessage::textTooLong();
        }

        $this->text = $text;
        $this->hasText = true;

        return $this;
    }

    /**
     * Add Attachment.
     *
     * @param $attachmentType
     * @param $url
     *
     * @throws CouldNotCreateMessage
     *
     * @return $this
     */
    public function attach($attachmentType, $url)
    {
        $attachmentTypes = [
            AttachmentType::FILE,
            AttachmentType::IMAGE,
            AttachmentType::VIDEO,
            AttachmentType::AUDIO,
        ];

        if (! in_array($attachmentType, $attachmentTypes)) {
            throw CouldNotCreateMessage::invalidAttachmentType();
        }

        if (! isset($url)) {
            throw CouldNotCreateMessage::urlNotProvided();
        }

        $this->notificationType = $attachmentType;
        $this->attachmentUrl = $url;
        $this->hasAttachment = true;

        return $this;
    }

    /**
     * Push notification type.
     *
     * @param string $notificationType Possible values: REGULAR, SILENT_PUSH, NO_PUSH
     *
     * @return $this
     */
    public function notificationType($notificationType)
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
     *
     * @throws CouldNotCreateMessage
     * @return array
     */
    public function toArray()
    {
        if ($this->hasAttachment) {
            return $this->attachmentMessageToArray();
        }

        if ($this->hasText) {
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
     *
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
     *
     * @return array
     */
    protected function attachmentMessageToArray()
    {
        $message = [];
        $message['recipient'] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['attachment']['type'] = $this->attachmentType;
        $message['message']['attachment']['payload']['url'] = $this->attachmentUrl;

        return $message;
    }

    /**
     * Returns message for Generic Template message.
     *
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
     *
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
