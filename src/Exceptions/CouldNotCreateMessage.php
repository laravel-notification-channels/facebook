<?php

namespace NotificationChannels\Facebook\Exceptions;

class CouldNotCreateMessage extends \Exception
{
    /**
     * Thrown when the message text is not provided.
     *
     * @return static
     */
    public static function textTooLong()
    {
        return new static('Message text is too long, A 320 character limited string should be provided.');
    }

    /**
     * Thrown when invalid notification type provided.
     *
     * @return static
     */
    public static function invalidNotificationType()
    {
        return new static('Notification Type provided is invalid.');
    }

    /**
     * Thrown when invalid attachment type provided.
     *
     * @return static
     */
    public static function invalidAttachmentType()
    {
        return new static('Attachment Type provided is invalid.');
    }

    /**
     * Thrown when a URl should be provided for an attachment.
     *
     * @return static
     */
    public static function urlNotProvided()
    {
        return new static('You have not provided a Url for an attachment');
    }

    /**
     * Thrown when a attachment type is not provided.
     *
     * @return static
     */
    public static function attachmentTypeNotProvided()
    {
        return new static('You have not provided a type for an attachment');
    }

    /**
     * Thrown when the button type is "postback" or "phone_number",
     * but the data value is not provided for the payload.
     *
     * @return static
     */
    public static function dataNotProvided()
    {
        return new static("Your message was missing critical information");
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
            "Your title was {$count} characters long, which exceeds the 20 character limit. Please check the button template docs for more information."
        );
    }

    /**
     * Thrown when the payload characters limit is exceeded.
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function payloadLimitExceeded($data)
    {
        $count = mb_strlen($data);

        return new static(
            "Your payload was {$count} characters long, which exceeds the 1000 character limit. Please check the button template docs for more information."
        );
    }

    /**
     * Thrown when number of buttons in message exceeds.
     *
     * @return static
     */
    public static function messageButtonsLimitExceeded()
    {
        return new static('You cannot add more than 3 buttons in 1 notification message.');
    }

    /**
     * Thrown when number of cards in message exceeds.
     *
     * @return static
     */
    public static function messageCardsLimitExceeded()
    {
        return new static('You cannot add more than 10 cards in 1 notification message.');
    }

    /**
     * Thrown when there is no user id or phone number provided.
     *
     * @return static
     */
    public static function recipientNotProvided()
    {
        return new static('Facebook notification recipient ID or Phone Number was not provided. Please refer usage docs.');
    }
}
