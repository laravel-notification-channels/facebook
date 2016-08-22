<?php

namespace NotificationChannels\Facebook\Components;

use NotificationChannels\Facebook\Exceptions\CouldNotCreateCard;
use NotificationChannels\Facebook\Traits\ButtonsTrait;

class Card implements \JsonSerializable
{
    use ButtonsTrait;

    /** @var string Card Title */
    protected $title;

    /** @var string Item Url */
    protected $image_url;

    /** @var string Image Url */
    protected $item_url;

    /** @var string Subtitle */
    protected $subtitle;

    /**
     * Create a Card.
     *
     * @return static
     */
    public static function create($title = '')
    {
        return new static($title);
    }

    /**
     * Create Card constructor.
     */
    public function __construct($title = '')
    {
        $this->title($title);
    }

    /**
     * Set Button Title.
     *
     * @param $title
     * @throws CouldNotCreateCard
     * @return $this
     */
    public function title($title)
    {
        if (mb_strlen($title) > 80) {
            throw CouldNotCreateCard::titleLimitExceeded($this->title);
        }
        $this->title = $title;

        return $this;
    }

    /**
     * Set Card Item Url.
     *
     * @param $item_url
     * @return $this
     */
    public function url($item_url)
    {
        $this->item_url = $item_url;

        return $this;
    }

    /**
     * Set Card Image Url.
     *
     * @param $image_url
     * Image ration should be 1.91:1
     * @return $this
     */
    public function image($image_url)
    {
        $this->image_url = $image_url;

        return $this;
    }

    /**
     * Set Card Subtitle.
     *
     * @param $subtitle
     * @throws CouldNotCreateCard
     * @return $this
     */
    public function subtitle($subtitle)
    {
        if (mb_strlen($subtitle) > 80) {
            throw CouldNotCreateCard::subtitleLimitExceeded($this->title);
        }
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Builds payload and returns an array.
     *
     * @return array
     * @throws CouldNotCreateCard
     */
    public function toArray()
    {
        $payload = [];

        if (! isset($this->title)) {
            throw CouldNotCreateCard::titleNotProvided();
        }
        $payload['title'] = $this->title;

        if (isset($this->item_url)) {
            $payload['item_url'] = $this->item_url;
        }

        if (isset($this->image_url)) {
            $payload['image_url'] = $this->image_url;
        }

        if (isset($this->subtitle)) {
            $payload['subtitle'] = $this->subtitle;
        }

        if (count($this->buttons) > 0) {
            $payload['buttons'] = $this->buttons;
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
}
