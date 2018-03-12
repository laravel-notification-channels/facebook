<?php

namespace NotificationChannels\Facebook\Test;

use NotificationChannels\Facebook\FacebookMessage;

class FacebookMessageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_sets_recipient_in_a_backwards_compatible_way()
    {
        $this->assertEquals(['id' => 'abc123'], (new FacebookMessage)->to('abc123')->recipient);

        $this->assertEquals(['id' => 'abc123'], (new FacebookMessage)->to(['id' => 'abc123'])->recipient);
    }
}
