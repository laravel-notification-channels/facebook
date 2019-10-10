<?php

namespace NotificationChannels\Facebook\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

/**
 * Class CouldNotSendNotification.
 */
class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when there's a bad request and an error is responded.
     *
     * @param  ClientException  $exception
     *
     * @return static
     */
    public static function facebookRespondedWithAnError(ClientException $exception): self
    {
        if ($exception->hasResponse()) {
            $result = json_decode($exception->getResponse()->getBody(), false);

            return new static("Facebook responded with an error `{$result->error->code} - {$result->error->type} {$result->error->message}`");
        }

        return new static('Facebook responded with an error');
    }

    /**
     * Thrown when there's no page token provided.
     *
     * @param  string  $message
     *
     * @return static
     */
    public static function facebookPageTokenNotProvided(string $message): self
    {
        return new static($message);
    }

    /**
     * Thrown when we're unable to communicate with Telegram.
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithFacebook(Exception $exception): self
    {
        return new static('The communication with Facebook failed. Reason: '.$exception->getMessage());
    }
}
