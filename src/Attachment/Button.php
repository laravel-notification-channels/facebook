<?php

namespace NotificationChannels\Facebook\Attachment;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateButton;

class Button implements \JsonSerializable
{
    /** @var string Button Title */
    protected $title;

    /** @var string|array Button URL, Postback Data or Phone Number */
    protected $data;

    /** @var string Button Type */
    protected $type;

    /** Button Type: web_url */
    const TYPE_WEB_URL = 'web_url';

    /** Button Type: postback */
    const TYPE_POSTBACK = 'postback';

    /** Button Type: phone_number */
    const TYPE_PHONE_NUMBER = 'phone_number';

    /**
     * Create a button.
     *
     * @param string       $title
     * @param string|array $data
     * @param string       $type
     *
     * @return static
     */
    public static function create($title = '', $data = null, $type = 'web_url')
    {
        return new static($title, $data, $type);
    }

    /**
     * Button constructor.
     *
     * @param string       $title
     * @param string|array $data
     * @param string       $type
     */
    public function __construct($title = '', $data = null, $type = 'web_url')
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
        $this->type = self::TYPE_WEB_URL;

        return $this;
    }

    /**
     * Set button type as postback..
     *
     * @return $this
     */
    public function isTypePostback()
    {
        $this->type = self::TYPE_POSTBACK;

        return $this;
    }

    /**
     * Set button type as phone_number.
     *
     * @return $this
     */
    public function isTypePhoneNumber()
    {
        $this->type = self::TYPE_PHONE_NUMBER;

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

        if ($this->isType(self::TYPE_WEB_URL)) {
            if (! isset($this->data)) {
                throw CouldNotCreateButton::urlNotProvided();
            }

            $payload['url'] = $this->data;
        } else {
            if (! isset($this->data)) {
                throw CouldNotCreateButton::dataNotProvided($this->type);
            }

            if ($this->isType(self::TYPE_PHONE_NUMBER)) {
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
            throw CouldNotCreateButton::titleLimitExceeded();
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
            throw CouldNotCreateButton::payloadLimitExceeded();
        }
    }

    /**
     * Validate Phone Number.
     *
     * @throws CouldNotCreateButton
     */
    protected function validatePhoneNumber()
    {
        if ($this->isType(self::TYPE_PHONE_NUMBER) && ! starts_with($this->data, '+')) {
            throw CouldNotCreateButton::invalidPhoneNumberProvided($this->data);
        }
    }
}
