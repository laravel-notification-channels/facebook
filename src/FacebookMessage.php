<?php

namespace NotificationChannels\Facebook;

use JsonSerializable;
use NotificationChannels\Facebook\Enums\AttachmentType;
use NotificationChannels\Facebook\Enums\ImageAspectRatioType;
use NotificationChannels\Facebook\Enums\MessagingType;
use NotificationChannels\Facebook\Enums\NotificationType;
use NotificationChannels\Facebook\Enums\RecipientType;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateMessage;
use NotificationChannels\Facebook\Traits\HasButtons;

/**
 * Class FacebookMessage.
 */
class FacebookMessage implements JsonSerializable
{
    use HasButtons;

    /** @var string Recipient's ID. */
    public $recipient;

    /** @var string Recipient Type */
    public $recipientType = RecipientType::ID;

    /** @var string Notification Text. */
    public $text;

    /** @var string Notification Type */
    public $notificationType = NotificationType::REGULAR;

    /** @var string Messaging Type. Defaults to UPDATE */
    protected $messagingType = MessagingType::UPDATE;

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

    /** @var bool There is card with 'image_url' in attachment */
    protected $hasImageUrl = false;

    /** @var string Message tag used with messaging type MESSAGE_TAG */
    protected $messageTag;

    /** @var string The aspect ratio for */
    protected $imageAspectRatio = ImageAspectRatioType::HORIZONTAL;

    /**
     * @param  string  $text
     *
     * @throws CouldNotCreateMessage
     * @return static
     */
    public static function create(string $text = ''): self
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
     * @param  string|array  $recipient  ID of recipient or Phone number of the recipient with the format
     *     +1(212)555-2368
     * @param  string        $type  Recipient Type: id, user_ref, phone_number, post_id, comment_id.
     *
     * @return $this
     */
    public function to($recipient, string $type = RecipientType::ID): self
    {
        if (is_array($recipient)) {
            [$type, $recipient] = $recipient;
        }

        $this->recipient = $recipient;
        $this->recipientType = $type;

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

        if (! in_array($attachmentType, $attachmentTypes, false)) {
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

        if (! in_array($notificationType, $notificationTypes, false)) {
            throw CouldNotCreateMessage::invalidNotificationType();
        }

        $this->notificationType = $notificationType;

        return $this;
    }

    public function imageAspectRatio(string $imageAspectRatio): self
    {
        $imageAspectRatios = [
            ImageAspectRatioType::SQUARE,
            ImageAspectRatioType::HORIZONTAL,
        ];

        if (! in_array($imageAspectRatio, $imageAspectRatios, false)) {
            throw CouldNotCreateMessage::invalidImageAspectRatio();
        }

        foreach ($this->cards as $card) {
            if (array_key_exists('image_url', $card->toArray())) {
                $this->hasImageUrl = true;
                break;
            }
        }

        if (! $this->hasImageUrl) {
            return $this;
        }

        $this->imageAspectRatio = $imageAspectRatio;

        return $this;
    }

    /**
     * Helper to set notification type as REGULAR.
     *
     * @return $this
     */
    public function isTypeRegular(): self
    {
        $this->notificationType = NotificationType::REGULAR;

        return $this;
    }

    /**
     * Helper to set notification type as SILENT_PUSH.
     *
     * @return $this
     */
    public function isTypeSilentPush(): self
    {
        $this->notificationType = NotificationType::SILENT_PUSH;

        return $this;
    }

    /**
     * Helper to set notification type as NO_PUSH.
     *
     * @return $this
     */
    public function isTypeNoPush(): self
    {
        $this->notificationType = NotificationType::NO_PUSH;

        return $this;
    }

    /**
     * Helper to set messaging type as RESPONSE.
     *
     * @return $this
     */
    public function isResponse(): self
    {
        $this->messagingType = MessagingType::RESPONSE;

        return $this;
    }

    /**
     * Helper to set messaging type as UPDATE.
     *
     * @return $this
     */
    public function isUpdate(): self
    {
        $this->messagingType = MessagingType::UPDATE;

        return $this;
    }

    /**
     * Helper to set messaging type as MESSAGE_TAG.
     *
     * @param $messageTag
     *
     * @return $this
     */
    public function isMessageTag($messageTag): self
    {
        $this->messagingType = MessagingType::MESSAGE_TAG;
        $this->messageTag = $messageTag;

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
        return ! isset($this->recipient);
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
            // check if it has buttons
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
        $message['recipient'][$this->recipientType] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['text'] = $this->text;
        $message['messaging_type'] = $this->messagingType;

        if (filled($this->messageTag)) {
            $message['tag'] = $this->messageTag;
        }

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
        $message['recipient'][$this->recipientType] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['attachment']['type'] = $this->attachmentType;
        $message['message']['attachment']['payload']['url'] = $this->attachmentUrl;
        $message['messaging_type'] = $this->messagingType;

        if (filled($this->messageTag)) {
            $message['tag'] = $this->messageTag;
        }

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
        $message['recipient'][$this->recipientType] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['attachment']['type'] = 'template';
        $message['message']['attachment']['payload']['template_type'] = 'generic';
        $message['message']['attachment']['payload']['elements'] = $this->cards;
        $message['messaging_type'] = $this->messagingType;

        if ($this->hasImageUrl) {
            $message['message']['attachment']['payload']['image_aspect_ratio'] = $this->imageAspectRatio;
        }

        if (filled($this->messageTag)) {
            $message['tag'] = $this->messageTag;
        }

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
        $message['recipient'][$this->recipientType] = $this->recipient;
        $message['notification_type'] = $this->notificationType;
        $message['message']['attachment']['type'] = 'template';
        $message['message']['attachment']['payload']['template_type'] = 'button';
        $message['message']['attachment']['payload']['text'] = $this->text;
        $message['message']['attachment']['payload']['buttons'] = $this->buttons;
        $message['messaging_type'] = $this->messagingType;

        if (filled($this->messageTag)) {
            $message['tag'] = $this->messageTag;
        }

        return $message;
    }
}
