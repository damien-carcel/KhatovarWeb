<?php

namespace spec\Khatovar\Component\User\Application\Command;

use Khatovar\Component\User\Application\Command\UserStatus;
use Khatovar\Component\User\Domain\Model\UserInterface;
use PhpSpec\ObjectBehavior;

class UserStatusSpec extends ObjectBehavior
{
    function let(UserInterface $user)
    {
        $this->beConstructedWith($user, true);
    }

    function it_is_a_user_status_command()
    {
        $this->shouldHaveType(UserStatus::class);
    }

    function it_returns_the_command_user($user)
    {
        $this->user()->shouldReturn($user);
    }

    function it_returns_the_command_status()
    {
        $this->status()->shouldReturn(true);
    }
}
