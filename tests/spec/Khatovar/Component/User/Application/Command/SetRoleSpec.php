<?php

declare(strict_types=1);

namespace spec\Khatovar\Component\User\Application\Command;

use Khatovar\Component\User\Application\Command\SetRole;
use Khatovar\Component\User\Domain\Model\UserInterface;
use PhpSpec\ObjectBehavior;

class SetRoleSpec extends ObjectBehavior
{
    function let(UserInterface $user)
    {
        $this->beConstructedWith($user, 'A_ROLE');
    }

    function it_is_a_set_role_command()
    {
        $this->shouldHaveType(SetRole::class);
    }

    function it_returns_the_command_user($user)
    {
        $this->user()->shouldReturn($user);
    }

    function it_returns_the_command_role()
    {
        $this->role()->shouldReturn('A_ROLE');
    }
}
