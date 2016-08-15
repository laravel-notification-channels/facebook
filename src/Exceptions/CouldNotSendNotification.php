<?php

namespace NotificationChannels\Facebook\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends \Exception
{
    /**
     * Thrown when there's a bad request and an error is responded.
     *
     * @param ClientException $exception
     *
     * @return static
     */
    public static function facebookRespondedWithAnError(ClientException $exception)
    {
        $result = json_decode($exception->getResponse()->getBody());

        return new static("Facebook responded with an error `{$result->error->code} - {$result->error->type} {$result->error->message}`");
    }

    /**
     * Thrown when there's no page token provided.
     *
     * @param string $message
     *
     * @return static
     */
    public static function facebookPageTokenNotProvided($message)
    {
        return new static($message);
    }

    /**
     * Thrown when we're unable to communicate with Telegram.
     *
     * @param \Exception $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithFacebook(Exception $exception)
    {
        return new static('The communication with Facebook failed. Reason: '.$exception->getMessage());
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
}
