<?php

namespace NotificationChannels\Facebook\Exceptions;

use Exception;

/**
 * Class CouldNotCreateCard.
 */
class CouldNotCreateCard extends Exception
{
    /**
     * Thrown when the button title is not provided.
     *
     * @return static
     */
    public static function titleNotProvided(): self
    {
        return new static('Button title was not provided, A 20 character limited title should be provided.');
    }

    /**
     * Thrown when the title characters limit is exceeded.
     *
     * @param  string  $title
     *
     * @return static
     */
    public static function titleLimitExceeded(string $title): self
    {
        $count = mb_strlen($title);

        return new static(
            "Your title was {$count} characters long, which exceeds the 80 character limit"
        );
    }

    /**
     * Thrown when the subtitle characters limit is exceeded.
     *
     * @param  string  $title
     *
     * @return static
     */
    public static function subtitleLimitExceeded(string $title): self
    {
        $count = mb_strlen($title);

        return new static(
            "Your subtitle was {$count} characters long, which exceeds the 80 character limit"
        );
    }
}
