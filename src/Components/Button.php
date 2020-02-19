<?php

namespace NotificationChannels\Facebook\Components;

use Illuminate\Support\Str;
use JsonSerializable;
use NotificationChannels\Facebook\Enums\ButtonType;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateButton;

/**
 * Class Button.
 */
class Button implements JsonSerializable
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
     * @param  string        $title
     * @param  string|array  $data
     * @param  string        $type
     *
     * @return static
     */
    public static function create(string $title = '', $data = null, string $type = ButtonType::WEB_URL): self
    {
        return new static($title, $data, $type);
    }

    /**
     * Button Constructor.
     *
     * @param  string        $title
     * @param  string|array  $data
     * @param  string        $type
     */
    public function __construct(string $title = '', $data = null, string $type = ButtonType::WEB_URL)
    {
        $this->title = $title;
        $this->data = $data;
        $this->payload['type'] = $type;
    }

    /**
     * Set Button Title.
     *
     * @param  string  $title
     *
     * @throws CouldNotCreateButton
     * @return $this
     */
    public function title(string $title): self
    {
        if (blank($title)) {
            throw CouldNotCreateButton::titleNotProvided();
        }

        if (mb_strlen($title) > 20) {
            throw CouldNotCreateButton::titleLimitExceeded($title);
        }

        $this->payload['title'] = $title;

        return $this;
    }

    /**
     * Set a URL for the button.
     *
     * @param  string  $url
     *
     * @throws CouldNotCreateButton
     * @return $this
     */
    public function url(string $url): self
    {
        if (blank($url)) {
            throw CouldNotCreateButton::urlNotProvided();
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw CouldNotCreateButton::invalidUrlProvided($url);
        }

        $this->payload['url'] = $url;
        $this->isTypeWebUrl();

        return $this;
    }

    /**
     * @param  string  $phone
     *
     * @throws CouldNotCreateButton
     * @return $this
     */
    public function phone(string $phone): self
    {
        if (blank($phone)) {
            throw CouldNotCreateButton::phoneNumberNotProvided();
        }

        if (is_string($phone) && ! Str::startsWith($phone, '+')) {
            throw CouldNotCreateButton::invalidPhoneNumberProvided($phone);
        }

        $this->payload['payload'] = $phone;
        $this->isTypePhoneNumber();

        return $this;
    }

    /**
     * @param  array  $postback
     *
     * @throws CouldNotCreateButton
     * @return $this
     */
    public function postback(array $postback): self
    {
        if (blank($postback)) {
            throw CouldNotCreateButton::postbackNotProvided();
        }

        $this->payload['payload'] = json_encode($postback);
        $this->isTypePostback();

        return $this;
    }

    /**
     * Set Button Type.
     *
     * @param  string  $type  Possible Values: "web_url", "postback" or "phone_number". Default: "web_url"
     *
     * @return $this
     */
    public function type(string $type): self
    {
        $this->payload['type'] = $type;

        return $this;
    }

    /**
     * Set button type as web_url.
     *
     * @return $this
     */
    public function isTypeWebUrl(): self
    {
        $this->payload['type'] = ButtonType::WEB_URL;

        return $this;
    }

    /**
     * Set button type as postback.
     *
     * @return $this
     */
    public function isTypePostback(): self
    {
        $this->payload['type'] = ButtonType::POSTBACK;

        return $this;
    }

    /**
     * Set button type as phone_number.
     *
     * @return $this
     */
    public function isTypePhoneNumber(): self
    {
        $this->payload['type'] = ButtonType::PHONE_NUMBER;

        return $this;
    }

    /**
     * Determine Button Type.
     *
     * @param  string  $type
     *
     * @return bool
     */
    protected function isType(string $type): bool
    {
        return isset($this->payload['type']) && $type === $this->payload['type'];
    }

    /**
     * Make payload by data and type.
     *
     * @param  mixed  $data
     *
     * @throws CouldNotCreateButton
     * @return $this
     */
    protected function makePayload($data): self
    {
        if (blank($data)) {
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
     * @throws CouldNotCreateButton
     * @return array
     */
    public function toArray(): array
    {
        $this->title($this->title);
        $this->makePayload($this->data);

        return $this->payload;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @throws CouldNotCreateButton
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
