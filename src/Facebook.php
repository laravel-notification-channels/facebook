<?php

namespace NotificationChannels\Facebook;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use NotificationChannels\Facebook\Exceptions\CouldNotSendNotification;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Facebook.
 */
class Facebook
{
    /** @var HttpClient HTTP Client */
    protected $http;

    /** @var string|null Page Token. */
    protected $token;

    /** @var string|null App Secret */
    protected $secret;

    /** @var string Default Graph API Version */
    protected $graphApiVersion = '4.0';

    /**
     * @param  string|null      $token
     * @param  HttpClient|null  $httpClient
     */
    public function __construct(string $token = null, HttpClient $httpClient = null)
    {
        $this->token = $token;

        $this->http = $httpClient;
    }

    /**
     * Set Default Graph API Version.
     *
     * @param $graphApiVersion
     *
     * @return Facebook
     */
    public function setGraphApiVersion($graphApiVersion): self
    {
        $this->graphApiVersion = $graphApiVersion;

        return $this;
    }

    /**
     * Set App Secret to generate appsecret_proof.
     *
     * @param string $secret
     *
     * @return Facebook
     */
    public function setSecret($secret = null): self
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get HttpClient.
     *
     * @return HttpClient
     */
    protected function httpClient(): HttpClient
    {
        return $this->http ?? new HttpClient();
    }

    /**
     * Send text message.
     *
     * @param  array  $params
     *
     * @throws GuzzleException
     * @throws CouldNotSendNotification
     * @return ResponseInterface
     */
    public function send(array $params): ResponseInterface
    {
        return $this->post('me/messages', $params);
    }

    /**
     * @param  string  $endpoint
     * @param  array   $params
     *
     * @throws GuzzleException
     * @throws CouldNotSendNotification
     * @return ResponseInterface
     */
    public function get(string $endpoint, array $params = []): ResponseInterface
    {
        return $this->api($endpoint, ['query' => $params]);
    }

    /**
     * @param  string  $endpoint
     * @param  array   $params
     *
     * @throws GuzzleException
     * @throws CouldNotSendNotification
     * @return ResponseInterface
     */
    public function post(string $endpoint, array $params = []): ResponseInterface
    {
        return $this->api($endpoint, ['json' => $params], 'POST');
    }

    /**
     * Send an API request and return response.
     *
     * @param  string  $endpoint
     * @param  array   $options
     * @param  string  $method
     *
     * @throws GuzzleException
     * @throws CouldNotSendNotification
     * @return mixed|ResponseInterface
     */
    protected function api(string $endpoint, array $options, $method = 'GET')
    {
        if (empty($this->token)) {
            throw CouldNotSendNotification::facebookPageTokenNotProvided('You must provide your Facebook Page token to make any API requests.');
        }

        $url = "https://graph.facebook.com/v{$this->graphApiVersion}/{$endpoint}?access_token={$this->token}";

        if ($this->secret) {
            $appsecret_proof = hash_hmac('sha256', $this->token, $this->secret);

            $url .= "&appsecret_proof={$appsecret_proof}";
        }

        try {
            return $this->httpClient()->request($method, $url, $options);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::facebookRespondedWithAnError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithFacebook($exception);
        }
    }
}
