<?php

namespace spec\QM\Logger\Logger;

use QM\Logger\Logger\Logger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use MongoDB;

class LoggerSpec extends ObjectBehavior
{
    function it_is_initializable($client)
    {
        $client->beADoubleOf(MongoDB\Client::class);
        $this->beConstructedWith($client, "test", "test");
        $this->shouldHaveType(Logger::class);
    }
}
