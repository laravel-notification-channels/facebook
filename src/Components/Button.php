<?php

namespace NotificationChannels\Facebook\Components;

use NotificationChannels\Facebook\Enums\ButtonType;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateButton;

class Button implements \JsonSerializable
{
    /** @var string Button Title */
    protected $title;

    /** @var string Button Type */
    protected $type;

    /** @var string|array Button URL, Postback Data or Phone Number */
    protected $data;

    /** @var array Payload */
    protected $payload = [];

    /**
     * Create a button.
     *
     * @param string       $title
     * @param string|array $data
     * @param string       $type
     *
     * @return static
     */
    public static function create($title = '', $data = null, $type = ButtonType::WEB_URL)
    {
        return new static($title, $data, $type);
    }

    /**
     * Button Constructor.
     *
     * @param string       $title
     * @param string|array $data
     * @param string       $type
     */
    public function __construct($title = '', $data = null, $type = ButtonType::WEB_URL)
    {
        $this->title = $title;
        $this->data = $data;
        $this->payload['type'] = $type;
    }

    /**
     * Set Button Title.
     *
     * @param $title
     *
     * @return $this
     * @throws CouldNotCreateButton
     */
    public function title($title)
    {
        if ($this->isNotSetOrEmpty($title)) {
            throw CouldNotCreateButton::titleNotProvided();
        } elseif (mb_strlen($title) > 20) {
            throw CouldNotCreateButton::titleLimitExceeded($title);
        }

        $this->payload['title'] = $title;

        return $this;
    }

    /**
     * Set a URL for the button.
     *
     * @param $url
     *
     * @return $this
     * @throws CouldNotCreateButton
     */
    public function url($url)
    {
        if ($this->isNotSetOrEmpty($url)) {
            throw CouldNotCreateButton::urlNotProvided();
        } elseif (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw CouldNotCreateButton::invalidUrlProvided($url);
        }

        $this->payload['url'] = $url;
        $this->isTypeWebUrl();

        return $this;
    }

    /**
     * @param $phone
     *
     * @return $this
     * @throws CouldNotCreateButton
     */
    public function phone($phone)
    {
        if ($this->isNotSetOrEmpty($phone)) {
            throw CouldNotCreateButton::phoneNumberNotProvided();
        } elseif (is_string($phone) && ! starts_with($phone, '+')) {
            throw CouldNotCreateButton::invalidPhoneNumberProvided($phone);
        }

        $this->payload['payload'] = $phone;
        $this->isTypePhoneNumber();

        return $this;
    }

    /**
     * @param $postback
     *
     * @return $this
     * @throws CouldNotCreateButton
     */
    public function postback($postback)
    {
        if ($this->isNotSetOrEmpty($postback)) {
            throw CouldNotCreateButton::postbackNotProvided();
        } elseif (! is_array($postback)) {
            throw CouldNotCreateButton::invalidPostbackProvided($postback);
        }

        $this->payload['payload'] = json_encode($postback);
        $this->isTypePostback();

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
        $this->payload['type'] = $type;

        return $this;
    }

    /**
     * Set button type as web_url.
     *
     * @return $this
     */
    public function isTypeWebUrl()
    {
        $this->payload['type'] = ButtonType::WEB_URL;

        return $this;
    }

    /**
     * Set button type as postback.
     *
     * @return $this
     */
    public function isTypePostback()
    {
        $this->payload['type'] = ButtonType::POSTBACK;

        return $this;
    }

    /**
     * Set button type as phone_number.
     *
     * @return $this
     */
    public function isTypePhoneNumber()
    {
        $this->payload['type'] = ButtonType::PHONE_NUMBER;

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
        return isset($this->payload['type']) && $type === $this->payload['type'];
    }

    /**
     * Make payload by data and type.
     *
     * @param mixed $data
     *
     * @return $this
     * @throws CouldNotCreateButton
     */
    protected function makePayload($data)
    {
        if ($this->isNotSetOrEmpty($data)) {
            return $this;
        }

        switch ($this->payload['type']) {
            case ButtonType::WEB_URL:
                $this->url($data);
                break;
            case ButtonType::PHONE_NUMBER:
                $this->phone($data);
                break;
            case ButtonType::POSTBACK:
                $this->postback($data);
                break;
        }

        if (isset($this->payload['payload']) && mb_strlen($this->payload['payload']) > 1000) {
            throw CouldNotCreateButton::payloadLimitExceeded($this->payload['payload']);
        }

        return $this;
    }

    /**
     * Builds payload and returns an array.
     *
     * @return array
     * @throws CouldNotCreateButton
     */
    public function toArray()
    {
        $this->title($this->title);
        $this->makePayload($this->data);

        return $this->payload;
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
     * Determine if it's not set or is empty.
     *
     * @param $var
     *
     * @return bool
     */
    protected function isNotSetOrEmpty($var)
    {
        return ! isset($var) || empty($var);
    }
}
