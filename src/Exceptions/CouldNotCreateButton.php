<?php

namespace NotificationChannels\Facebook\Exceptions;

class CouldNotCreateButton extends \Exception
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
     * Thrown when the button type is "web_url" but the url is not provided.
     *
     * @return static
     */
    public static function urlNotProvided()
    {
        return new static('Your button type is `web_url` but the url is not provided.');
    }

    /**
     * Thrown when the button type is "postback" or "phone_number",
     * but the data value is not provided for the payload.
     *
     * @param $type
     *
     * @return static
     */
    public static function dataNotProvided($type)
    {
        return new static("Your button type is `{$type}` but the payload is not provided.");
    }

    /**
     * Thrown when the title characters limit is exceeded.
     *
     * @return static
     */
    public static function titleLimitExceeded()
    {
        return new static(
            'Title size should not exceed 20 characters. Please check the button template docs for more information.'
        );
    }

    /**
     * Thrown when the payload characters limit is exceeded.
     *
     * @return static
     */
    public static function payloadLimitExceeded()
    {
        return new static(
            'Payload should not exceed 1000 characters. Please check the button template docs for more information.'
        );
    }

    /**
     * Thrown when the phone number provided is of invalid format.
     *
     * @param $phoneNumber
     *
     * @return static
     */
    public static function invalidPhoneNumberProvided($phoneNumber)
    {
        return new static(
            "Provided phone number `{$phoneNumber}` format is invalid.".
            "Format must be '+' prefix followed by the country code, area code and local number.".
            "Please check the button template docs for more information."
        );
    }
}
