<?php

declare(strict_types=1);

namespace spec\Khatovar\Bundle\UserBundle\Application\Query;

use Khatovar\Bundle\UserBundle\Application\Query\GetUser;
use Khatovar\Bundle\UserBundle\Entity\Exception\UserDoesNotExist;
use Khatovar\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use Khatovar\Bundle\UserBundle\Entity\UserInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetUserSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, TokenStorageInterface $tokenStorage)
    {
        $this->beConstructedWith($userRepository, $tokenStorage);
    }

    function it_is_a_get_user_query()
    {
        $this->shouldHaveType(GetUser::class);
    }

    function it_gets_a_regular_user_by_its_username(
        $userRepository,
        $tokenStorage,
        UserInterface $user
    ) {
        $userRepository->findOneByUsername('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(false);

        $tokenStorage->getToken()->shouldNotBeCalled();

        $this->byUsername('username')->shouldReturn($user);
    }

    function it_gets_a_super_admin_user_by_its_username_if_user_in_security_token_storage_is_super_admin(
        $userRepository,
        $tokenStorage,
        UserInterface $user,
        TokenInterface $token,
        UserInterface $anotherUser
    ) {
        $userRepository->findOneByUsername('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(true);

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($anotherUser);
        $anotherUser->isSuperAdmin()->willReturn(true);

        $this->byUsername('username')->shouldReturn($user);
    }

    function it_gets_a_super_admin_user_by_its_username_if_there_is_no_user_in_security_token_storage(
        $userRepository,
        $tokenStorage,
        UserInterface $user,
        TokenInterface $token
    ) {
        $userRepository->findOneByUsername('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(true);

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);

        $this->byUsername('username')->shouldReturn($user);
    }

    function it_gets_a_super_admin_user_by_its_username_if_there_is_no_token_in_security_token_storage(
        $userRepository,
        $tokenStorage,
        UserInterface $user
    ) {
        $userRepository->findOneByUsername('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(true);

        $tokenStorage->getToken()->willReturn(null);

        $this->byUsername('username')->shouldReturn($user);
    }

    function it_throws_an_exception_if_user_does_not_exists($userRepository)
    {
        $userRepository->findOneByUsername('username')->willReturn(null);

        $exception = new UserDoesNotExist('username');
        $this->shouldThrow($exception)->during('byUsername', ['username']);
    }

    function it_throws_an_exception_if_a_regular_user_tries_to_get_a_super_admin(
        $userRepository,
        $tokenStorage,
        UserInterface $user,
        TokenInterface $token,
        UserInterface $anotherUser
    ) {
        $userRepository->findOneByUsername('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(true);

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($anotherUser);
        $anotherUser->isSuperAdmin()->willReturn(false);

        $exception = new AccessDeniedException('You do not have the permission to get user "username".');
        $this->shouldThrow($exception)->during('byUsername', ['username']);
    }
}
