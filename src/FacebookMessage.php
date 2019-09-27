<?php

namespace NotificationChannels\Facebook;

use JsonSerializable;
use NotificationChannels\Facebook\Traits\HasButtons;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;
use NotificationChannels\Facebook\Enums\{AttachmentType, NotificationType};

/**
 * Class FacebookMessage.
 */
class FacebookMessage implements JsonSerializable
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
     * @param  string  $text
     *
     * @throws CouldNotCreateMessage
     * @return static
     */
    public static function create(string $text = ''): FacebookMessage
    {
        return new static($text);
    }

    /**
     * @param  string  $text
     *
     * @throws CouldNotCreateMessage
     */
    public function __construct(string $text = '')
    {
        if ($text !== '') {
            $this->text($text);
        }
    }

    /**
     * Recipient's PSID or Phone Number.
     *
     * The id must be an ID that was retrieved through the
     * Messenger entry points or through the Messenger webhooks.
     *
     * @param  string  $recipient  ID of recipient or Phone number of the recipient
     *                   with the format +1(212)555-2368
     *
     * @return $this
     */
    public function to(string $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Notification text.
     *
     * @param  string  $text
     *
     * @throws CouldNotCreateMessage
     *
     * @return $this
     */
    public function text(string $text): self
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
     * @param  string  $attachmentType
     * @param  string  $url
     *
     * @throws CouldNotCreateMessage
     *
     * @return $this
     */
    public function attach(string $attachmentType, string $url): self
    {
        $attachmentTypes = [
            AttachmentType::FILE,
            AttachmentType::IMAGE,
            AttachmentType::VIDEO,
            AttachmentType::AUDIO,
        ];

        if (!in_array($attachmentType, $attachmentTypes, false)) {
            throw CouldNotCreateMessage::invalidAttachmentType();
        }

        if (blank($url)) {
            throw CouldNotCreateMessage::urlNotProvided();
        }

        $this->attachmentType = $attachmentType;
        $this->attachmentUrl = $url;
        $this->hasAttachment = true;

        return $this;
    }

    /**
     * Push notification type.
     *
     * @param  string  $notificationType  Possible values: REGULAR, SILENT_PUSH, NO_PUSH
     *
     * @throws CouldNotCreateMessage
     * @return $this
     */
    public function notificationType(string $notificationType): self
    {
        $notificationTypes = [
            NotificationType::REGULAR,
            NotificationType::SILENT_PUSH,
            NotificationType::NO_PUSH,
        ];

        if (!in_array($notificationType, $notificationTypes, false)) {
            throw CouldNotCreateMessage::invalidNotificationType();
        }

        $this->notificationType = $notificationType;

        return $this;
    }

    /**
     * Add up to 10 cards to be displayed in a carousel.
     *
     * @param  array  $cards
     *
     * @throws CouldNotCreateMessage
     * @return $this
     */
    public function cards(array $cards): self
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
    public function toNotGiven(): bool
    {
        return !isset($this->recipient);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @throws CouldNotCreateMessage
     * @return mixed
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
    public function toArray(): array
    {
        if ($this->hasAttachment) {
            return $this->attachmentMessageToArray();
        }

        if ($this->hasText) {
            //check if it has buttons
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
    protected function textMessageToArray(): array
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
    protected function attachmentMessageToArray(): array
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
    protected function genericMessageToArray(): array
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
    protected function buttonMessageToArray(): array
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
