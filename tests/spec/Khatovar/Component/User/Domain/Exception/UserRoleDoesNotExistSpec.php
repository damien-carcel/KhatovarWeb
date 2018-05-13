<?php

namespace spec\Khatovar\Component\User\Domain\Exception;

use Khatovar\Component\User\Domain\Exception\UserRoleDoesNotExist;
use PhpSpec\ObjectBehavior;

class UserRoleDoesNotExistSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('foobar');
    }

    function it_is_a_user_role_does_not_exist_exception()
    {
        $this->shouldHaveType(UserRoleDoesNotExist::class);
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_creates_an_exception_message_during_instanciation()
    {
        $this->getMessage()->shouldReturn('The user role "foobar" does not exist');
    }
}
