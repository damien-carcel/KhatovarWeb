<?php

namespace spec\Khatovar\Bundle\UserBundle\Security\Authentication;

use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Bundle\UserBundle\Security\Authentication\UserInSecurityTokenStorage;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserInSecurityTokenStorageSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage)
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_provides_the_current_user()
    {
        $this->shouldHaveType(UserInSecurityTokenStorage::class);
        $this->shouldImplement(CurrentTokenUser::class);
    }

    function it_gets_the_user_in_security_token_storage(
        $tokenStorage,
        TokenInterface $token,
        UserInterface $user
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->getFromTokenStorage()->shouldReturn($user);
    }

    function it_gets_no_user_if_token_is_empty(
        $tokenStorage,
        TokenInterface $token
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);

        $this->getFromTokenStorage()->shouldReturn(null);
    }

    function it_gets_no_user_if_token_does_not_contain_an_object(
        $tokenStorage,
        TokenInterface $token
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn('foobar');

        $this->getFromTokenStorage()->shouldReturn(null);
    }

    function it_gets_no_user_if_there_is_no_token_in_storage($tokenStorage)
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->getFromTokenStorage()->shouldReturn(null);
    }

    function it_returns_that_user_in_token_storage_is_super_admin(
        $tokenStorage,
        TokenInterface $token,
        UserInterface $user
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->isSuperAdmin()->willReturn(true);

        $this->isSuperAdmin()->shouldReturn(true);
    }

    function it_returns_that_user_in_token_storage_is_not_super_admin(
        $tokenStorage,
        TokenInterface $token,
        UserInterface $user
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->isSuperAdmin()->willReturn(false);

        $this->isSuperAdmin()->shouldReturn(false);
    }

    function it_returns_that_anonymous_user_in_token_storage_is_not_super_admin(
        $tokenStorage,
        TokenInterface $token
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn('foobar');

        $this->isSuperAdmin()->shouldReturn(false);
    }

    function it_returns_that_no_token_is_super_admin($tokenStorage)
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->isSuperAdmin()->shouldReturn(true);
    }
}
