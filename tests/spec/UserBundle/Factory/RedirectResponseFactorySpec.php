<?php

namespace spec\Khatovar\Bundle\UserBundle\Factory;

use Khatovar\Bundle\UserBundle\Factory\RedirectResponseFactory;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectResponseFactorySpec extends ObjectBehavior
{
    function it_is_a_redirect_response_factory()
    {
        $this->shouldHaveType(RedirectResponseFactory::class);
    }

    function it_creates_a_redirect_response()
    {
        $response = $this->create('/foobar');

        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/foobar');
    }
}
