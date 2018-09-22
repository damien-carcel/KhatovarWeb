<?php

declare(strict_types=1);

namespace spec\Khatovar\Component\User\Application\Query;

use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Component\User\Application\Query\GetUser;
use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetUserSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, CurrentTokenUser $currentTokenUser)
    {
        $this->beConstructedWith($userRepository, $currentTokenUser);
    }

    function it_is_a_get_user_query()
    {
        $this->shouldHaveType(GetUser::class);
    }

    function it_gets_a_regular_user_by_its_username(
        $userRepository,
        $currentTokenUser,
        UserInterface $user
    ) {
        $userRepository->get('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(false);

        $currentTokenUser->isSuperAdmin()->shouldNotBeCalled();

        $this->byUsername('username')->shouldReturn($user);
    }

    function it_gets_a_super_admin_user_by_its_username_if_user_in_security_token_storage_is_super_admin(
        $userRepository,
        $currentTokenUser,
        UserInterface $user
    ) {
        $userRepository->get('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(true);

        $currentTokenUser->isSuperAdmin()->willReturn(true);

        $this->byUsername('username')->shouldReturn($user);
    }

    function it_throws_an_exception_if_user_does_not_exists($userRepository)
    {
        $exception = new UserDoesNotExist('username');

        $userRepository->get('username')->willThrow($exception);

        $this->shouldThrow($exception)->during('byUsername', ['username']);
    }

    function it_throws_an_exception_if_a_regular_user_tries_to_get_a_super_admin(
        $userRepository,
        $currentTokenUser,
        UserInterface $user
    ) {
        $userRepository->get('username')->willReturn($user);
        $user->isSuperAdmin()->willReturn(true);

        $currentTokenUser->isSuperAdmin()->willReturn(false);

        $exception = new AccessDeniedException('You do not have the permission to get user "username".');
        $this->shouldThrow($exception)->during('byUsername', ['username']);
    }
}
