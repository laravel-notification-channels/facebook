<?php

namespace NotificationChannels\Facebook\Components;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateButton;
use NotificationChannels\Facebook\Enums\ButtonType;

class Button implements \JsonSerializable
{
    /** @var string Button Title */
    protected $title;

    /** @var string|array Button URL, Postback Data or Phone Number */
    protected $data;

    /** @var string Button Type */
    protected $type;

    /**
     * Create a button.
     *
     * @param string $title
     * @param string|array $data
     * @param string $type
     *
     * @return static
     */
    public static function create($title = '', $data = null, $type = ButtonType::WEB_URL)
    {
        return new static($title, $data, $type);
    }

    /**
     * @param string $title
     * @param string|array $data
     * @param string $type
     */
    public function __construct($title = '', $data = null, $type = ButtonType::WEB_URL)
    {
        $this->title = $title;
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Set Button Title.
     *
     * @param $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set Button Data.
     * Could be url, postback or phone number.
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set Button Type.
     *
     * @param $type Possible Values: "web_url", "postback" or "phone_number". Default: "web_url"
     *
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set button type as web_url.
     *
     * @return $this
     */
    public function isTypeWebUrl()
    {
        $this->type = ButtonType::WEB_URL;

        return $this;
    }

    /**
     * Set button type as postback.
     *
     * @return $this
     */
    public function isTypePostback()
    {
        $this->type = ButtonType::POSTBACK;

        return $this;
    }

    /**
     * Set button type as phone_number.
     *
     * @return $this
     */
    public function isTypePhoneNumber()
    {
        $this->type = ButtonType::PHONE_NUMBER;

        return $this;
    }

    /**
     * Determine Button Type.
     *
     * @param $type
     *
     * @return bool
     */
    protected function isType($type)
    {
        return $this->type === $type;
    }

    /**
     * Builds payload and returns an array.
     *
     * @return array
     * @throws CouldNotCreateButton
     */
    public function toArray()
    {
        $payload = [];
        $payload['type'] = $this->type;

        if (! isset($this->title)) {
            throw CouldNotCreateButton::titleNotProvided();
        }

        $this->validateTitle();
        $payload['title'] = $this->title;

        if ($this->isType(ButtonType::WEB_URL)) {
            if (! isset($this->data)) {
                throw CouldNotCreateButton::urlNotProvided();
            }
            $payload['url'] = $this->data;
        } else {
            if (! isset($this->data)) {
                throw CouldNotCreateButton::dataNotProvided($this->type);
            }
            if ($this->isType(ButtonType::PHONE_NUMBER)) {
                $this->validatePhoneNumber();
                $payload['payload'] = $this->data;
            } else {
                $payload['payload'] = $this->data = json_encode($this->data);
                $this->validatePayload();
            }
        }

        return $payload;
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
     * Validate Title.
     *
     * @throws CouldNotCreateButton
     */
    protected function validateTitle()
    {
        if (mb_strlen($this->title) > 20) {
            throw CouldNotCreateButton::titleLimitExceeded($this->title);
        }
    }

    /**
     * Validate Payload.
     *
     * @throws CouldNotCreateButton
     */
    protected function validatePayload()
    {
        if (mb_strlen($this->data) > 1000) {
            throw CouldNotCreateButton::payloadLimitExceeded($this->data);
        }
    }

    /**
     * Validate Phone Number.
     *
     * @throws CouldNotCreateButton
     */
    protected function validatePhoneNumber()
    {
        if ($this->isType(ButtonType::PHONE_NUMBER) && is_string($this->data) && ! starts_with($this->data, '+')) {
            throw CouldNotCreateButton::invalidPhoneNumberProvided($this->data);
        }
    }
}
