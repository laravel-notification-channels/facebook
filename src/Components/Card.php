<?php

namespace NotificationChannels\Facebook\Components;

use NotificationChannels\Facebook\Traits\HasButtons;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateCard;

class Card implements \JsonSerializable
{
    use HasButtons;

    /** @var array Payload */
    protected $payload = [];

    /**
     * Create a Card.
     *
     * @param string $title
     *
     * @return static
     */
    public static function create($title = '')
    {
        return new static($title);
    }

    /**
     * Create Card constructor.
     *
     * @param string $title
     *
     * @throws CouldNotCreateCard
     */
    public function __construct($title = '')
    {
        if ($title !== '') {
            $this->title($title);
        }
    }

    /**
     * Set Button Title.
     *
     * @param $title
     *
     * @throws CouldNotCreateCard
     * @return $this
     */
    public function title($title)
    {
        if (mb_strlen($title) > 80) {
            throw CouldNotCreateCard::titleLimitExceeded($title);
        }

        $this->payload['title'] = $title;

        return $this;
    }

    /**
     * Set Card Item Url.
     *
     * @param $itemUrl
     *
     * @return $this
     */
    public function url($itemUrl)
    {
        $this->payload['item_url'] = $itemUrl;

        return $this;
    }

    /**
     * Set Card Image Url.
     *
     * @param $imageUrl Image ration should be 1.91:1
     *
     * @return $this
     */
    public function image($imageUrl)
    {
        $this->payload['image_url'] = $imageUrl;

        return $this;
    }

    /**
     * Set Card Subtitle.
     *
     * @param $subtitle
     *
     * @throws CouldNotCreateCard
     * @return $this
     */
    public function subtitle($subtitle)
    {
        if (mb_strlen($subtitle) > 80) {
            throw CouldNotCreateCard::subtitleLimitExceeded($subtitle);
        }

        $this->payload['subtitle'] = $subtitle;

        return $this;
    }

    /**
     * Returns a payload for API request.
     *
     * @return array
     * @throws CouldNotCreateCard
     */
    public function toArray()
    {
        if (!isset($this->payload['title'])) {
            throw CouldNotCreateCard::titleNotProvided();
        }

        if (count($this->buttons) > 0) {
            $this->payload['buttons'] = $this->buttons;
        }

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
}
