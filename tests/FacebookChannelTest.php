<?php

namespace NotificationChannels\Facebook\Test;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Container\Container;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\Facebook\FacebookChannel;
use NotificationChannels\Facebook\FacebookMessage;

class FacebookChannelTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function you_can_swap_the_underlying_facebook_instance_with_a_from_parameter()
    {

        $message = FacebookMessage::create('my text')->to('12345')->from('abc123');
        $mock_notification = Mockery::mock(Notification::class, function ($m) use ($message) {
            $m->shouldReceive('toFacebook')->with('notifiable')->andReturn($message);
        });
        $http_client_spy = Mockery::spy(HttpClient::class);
        Container::getInstance()->instance(HttpClient::class, $http_client_spy);

        Container::getInstance()->make(FacebookChannel::class, [])->send('notifiable', $mock_notification);

        $http_client_spy->shouldHaveReceived('request')->with('POST', Mockery::on(function ($arg) {
            return ends_with($arg, 'abc123');
        }), Mockery::any());
    }
}
