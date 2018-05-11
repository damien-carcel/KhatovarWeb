<?php

declare(strict_types=1);

namespace spec\Khatovar\Bundle\UserBundle\Factory;

use Khatovar\Bundle\UserBundle\Factory\SwiftMessageFactory;
use PhpSpec\ObjectBehavior;

class SwiftMessageFactorySpec extends ObjectBehavior
{
    function it_is_swift_message_factory()
    {
        $this->shouldHaveType(SwiftMessageFactory::class);
    }

    function it_creates_a_swift_message()
    {
        $message = $this->create('from@something.somewhere', 'to@something.somewhere', 'subject', 'body');

        $message->shouldBeAnInstanceOf(\Swift_Message::class);
        $message->getFrom()->shouldBe(['from@something.somewhere' => null]);
        $message->getTo()->shouldBe(['to@something.somewhere' => null]);
        $message->getSubject()->shouldBe('subject');
        $message->getBody()->shouldBe('body');
    }
}
