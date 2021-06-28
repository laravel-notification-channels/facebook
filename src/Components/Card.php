<?php

namespace NotificationChannels\Facebook\Components;

use JsonSerializable;
use NotificationChannels\Facebook\Exceptions\CouldNotCreateCard;
use NotificationChannels\Facebook\Traits\HasButtons;

/**
 * Class Card.
 */
class Card implements JsonSerializable
{
    use HasButtons;

    /** @var array Payload */
    protected $payload = [];

    /**
     * Create a Card.
     *
     * @param  string  $title
     *
     * @throws CouldNotCreateCard
     * @return static
     */
    public static function create(string $title = ''): self
    {
        return new static($title);
    }

    /**
     * Create Card constructor.
     *
     * @param  string  $title
     *
     * @throws CouldNotCreateCard
     */
    public function __construct(string $title = '')
    {
        if ($title !== '') {
            $this->title($title);
        }
    }

    /**
     * Set Button Title.
     *
     * @param  string  $title
     *
     * @throws CouldNotCreateCard
     * @return $this
     */
    public function title(string $title): self
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
     * @param  string  $itemUrl
     *
     * @return $this
     */
    public function url(string $itemUrl): self
    {
        $this->payload['item_url'] = $itemUrl;

        return $this;
    }

    /**
     * Set Card Image Url.
     *
     * @param  string  $imageUrl  Image ratio should be 1.91:1
     *
     * @return $this
     */
    public function image(string $imageUrl): self
    {
        $this->payload['image_url'] = $imageUrl;

        return $this;
    }

    /**
     * Set Card default action
     *
     * @param  string  $url
     *
     * @return $this
     */
    public function default(string $url): self
    {
        $this->payload['default_action']['type'] = 'web_url';
        $this->payload['default_action']['url'] = 'url';
        $this->payload['default_action']['webview_height_ratio'] = 'tall';

        return $this;
    }


    /**
     * Set Card Subtitle.
     *
     * @param  string  $subtitle
     *
     * @throws CouldNotCreateCard
     * @return $this
     */
    public function subtitle(string $subtitle): self
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
     * @throws CouldNotCreateCard
     * @return array
     */
    public function toArray(): array
    {
        if (! isset($this->payload['title'])) {
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
     * @throws CouldNotCreateCard
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
