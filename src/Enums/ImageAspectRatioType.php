<?php

namespace NotificationChannels\Facebook\Enums;

/**
 * Class ImageAspectRatioType.
 *
 * The aspect ratio used to render images specified by element.image_url
 *
 * @see https://developers.facebook.com/docs/messenger-platform/reference/templates/generic#payload
 */
class ImageAspectRatioType
{
    /**
     * Aspect ratio of image should be 1.91:1
     */
    public const HORIZONTAL = 'horizontal';
    /**
     * Aspect ratio of image should be 1:1
     */
    public const SQUARE = 'square';
}
