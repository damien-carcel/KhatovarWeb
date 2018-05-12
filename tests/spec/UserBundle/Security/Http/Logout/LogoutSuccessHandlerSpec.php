<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Khatovar\Bundle\UserBundle\Security\Http\Logout;

use Khatovar\Bundle\UserBundle\Factory\RedirectResponseFactory;
use Khatovar\Bundle\UserBundle\Security\Http\Logout\LogoutSuccessHandler;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class LogoutSuccessHandlerSpec extends ObjectBehavior
{
    function let(RedirectResponseFactory $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_a_logout_success_handler()
    {
        $this->shouldHaveType(LogoutSuccessHandler::class);
        $this->shouldImplement(LogoutSuccessHandlerInterface::class);
    }

    function it_redirect_the_user_to_a_url_after_logout(
        $factory,
        Request $request,
        HeaderBag $headerBag,
        RedirectResponse $response
    ) {
        $request->headers = $headerBag;
        $headerBag->get('referer')->willReturn('/foo/bar');

        $factory->create('/foo/bar')->shouldBeCalled()->willReturn($response);

        $this->onLogoutSuccess($request)->shouldReturn($response);
    }

    function it_redirect_the_user_to_the_root(
        $factory,
        Request $request,
        HeaderBag $headerBag,
        RedirectResponse $response
    ) {
        $request->headers = $headerBag;
        $headerBag->get('referer')->willReturn(null);

        $factory->create('/')->shouldBeCalled()->willReturn($response);

        $this->onLogoutSuccess($request)->shouldReturn($response);
    }
}
