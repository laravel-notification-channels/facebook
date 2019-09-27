<?php

namespace NotificationChannels\Facebook\Exceptions;

use Exception;

/**
 * Class CouldNotCreateMessage
 */
class CouldNotCreateMessage extends Exception
{
    /**
     * Thrown when the message text is not provided.
     *
     * @return static
     */
    public static function textTooLong(): CouldNotCreateMessage
    {
        return new static('Message text is too long, A 320 character limited string should be provided.');
    }

    /**
     * Thrown when invalid notification type provided.
     *
     * @return static
     */
    public static function invalidNotificationType(): CouldNotCreateMessage
    {
        return new static('Notification Type provided is invalid.');
    }

    /**
     * Thrown when invalid attachment type provided.
     *
     * @return static
     */
    public static function invalidAttachmentType(): CouldNotCreateMessage
    {
        return new static('Attachment Type provided is invalid.');
    }

    /**
     * Thrown when a URl should be provided for an attachment.
     *
     * @return static
     */
    public static function urlNotProvided(): CouldNotCreateMessage
    {
        return new static('You have not provided a Url for an attachment');
    }

    /**
     * Thrown when enough data is not provided.
     *
     * @return static
     */
    public static function dataNotProvided(): CouldNotCreateMessage
    {
        return new static('Your message was missing critical information');
    }

    /**
     * Thrown when number of buttons in message exceeds.
     *
     * @return static
     */
    public static function messageButtonsLimitExceeded(): CouldNotCreateMessage
    {
        return new static('You cannot add more than 3 buttons in 1 notification message.');
    }

    /**
     * Thrown when number of cards in message exceeds.
     *
     * @return static
     */
    public static function messageCardsLimitExceeded(): CouldNotCreateMessage
    {
        return new static('You cannot add more than 10 cards in 1 notification message.');
    }

    /**
     * Thrown when there is no user id or phone number provided.
     *
     * @return static
     */
    public static function recipientNotProvided(): CouldNotCreateMessage
    {
        return new static('Facebook notification recipient ID or Phone Number was not provided. Please refer usage docs.');
    }
}
