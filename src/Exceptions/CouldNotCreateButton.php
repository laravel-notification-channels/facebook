<?php

namespace NotificationChannels\Facebook\Exceptions;

use Exception;

/**
 * Class CouldNotCreateButton.
 */
class CouldNotCreateButton extends Exception
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
     * Thrown when the button type is "web_url" but the url is not provided.
     *
     * @return static
     */
    public static function urlNotProvided(): self
    {
        return new static('Your button type is `web_url` but the url is not provided.');
    }

    /**
     * Thrown when the button type is "phone_number" but the number is not provided.
     *
     * @return static
     */
    public static function phoneNumberNotProvided(): self
    {
        return new static('Your button type is `phone_number` but the phone number is not provided.');
    }

    /**
     * Thrown when the button type is "postback" but the postback data is not provided.
     *
     * @return static
     */
    public static function postbackNotProvided(): self
    {
        return new static('Your button type is `postback` but the postback data is not provided.');
    }

    /**
     * Thrown when the title characters limit is exceeded.
     *
     * @param string $title
     *
     * @return static
     */
    public static function titleLimitExceeded(string $title): self
    {
        $count = mb_strlen($title);

        return new static(
            "Your title was {$count} characters long, which exceeds the 20 character limit. Please check the button template docs for more information."
        );
    }

    /**
     * Thrown when the payload characters limit is exceeded.
     *
     * @param  mixed  $data
     *
     * @return static
     */
    public static function payloadLimitExceeded($data): self
    {
        $count = mb_strlen($data);

        return new static(
            "Your payload was {$count} characters long, which exceeds the 1000 character limit. Please check the button template docs for more information."
        );
    }

    /**
     * Thrown when the URL provided is not valid.
     *
     * @param string $url
     *
     * @return static
     */
    public static function invalidUrlProvided(string $url): self
    {
        return new static("`{$url}` is not a valid URL. Please check and provide a valid URL");
    }

    /**
     * Thrown when the phone number provided is of invalid format.
     *
     * @param string $phoneNumber
     *
     * @return static
     */
    public static function invalidPhoneNumberProvided(string $phoneNumber): self
    {
        return new static(
            "Provided phone number `{$phoneNumber}` format is invalid.".
            "Format must be '+' prefix followed by the country code, area code and local number.".
            'Please check the button template docs for more information.'
        );
    }
}
