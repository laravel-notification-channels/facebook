<?php

namespace NotificationChannels\Facebook\Exceptions;

class CouldNotCreateCard extends \Exception
{
    /**
     * Thrown when the button title is not provided.
     *
     * @return static
     */
    public static function titleNotProvided()
    {
        return new static('Button title was not provided, A 20 character limited title should be provided.');
    }

    /**
     * Thrown when the title characters limit is exceeded.
     *
     * @return static
     */
    public static function titleLimitExceeded($title)
    {
        $count = mb_strlen($title);

        return new static(
            "Your title was {$count} characters long, which exceeds the 80 character limit"
        );
    }

    /**
     * Thrown when the subtitle characters limit is exceeded.
     *
     * @return static
     */
    public static function subtitleLimitExceeded($title)
    {
        $count = mb_strlen($title);

        return new static(
            "Your subtitle was {$count} characters long, which exceeds the 80 character limit"
        );
    }
}
