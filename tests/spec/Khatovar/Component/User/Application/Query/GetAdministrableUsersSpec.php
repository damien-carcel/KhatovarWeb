<?php

declare(strict_types=1);

namespace spec\Khatovar\Component\User\Application\Query;

use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Component\User\Application\Query\GetAdministrableUsers;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GetAdministrableUsersSpec extends ObjectBehavior
{
    function let(CurrentTokenUser $currentTokenUser, UserRepositoryInterface $userRepository)
    {
        $this->beConstructedWith($currentTokenUser, $userRepository);
    }

    function it_queries_administrable_users()
    {
        $this->shouldHaveType(GetAdministrableUsers::class);
    }

    function it_returns_no_administrable_users_if_there_is_no_user_in_token_storage(
        $currentTokenUser,
        $userRepository
    ) {
        $currentTokenUser->getFromTokenStorage()->willReturn(null);

        $userRepository->findByRole(Argument::any())->shouldNotBeCalled();
        $userRepository->findAllBut(Argument::any())->shouldNotBeCalled();

        $this->forCurrentOne()->shouldReturn([]);
    }

    function it_returns_no_administrable_users_for_non_administrator(
        $currentTokenUser,
        $userRepository,
        UserInterface $currentUser
    ) {
        $currentTokenUser->getFromTokenStorage()->willReturn($currentUser);
        $currentUser->hasRole('ROLE_ADMIN')->willReturn(false);
        $currentUser->isSuperAdmin()->willReturn(false);

        $userRepository->findByRole(Argument::any())->shouldNotBeCalled();
        $userRepository->findAllBut(Argument::any())->shouldNotBeCalled();

        $this->forCurrentOne()->shouldReturn([]);
    }

    function it_returns_administrable_users_for_regular_administrator(
        $currentTokenUser,
        $userRepository,
        UserInterface $currentUser,
        UserInterface $superAdminUser,
        UserInterface $regularUser
    ) {
        $currentTokenUser->getFromTokenStorage()->willReturn($currentUser);
        $currentUser->hasRole('ROLE_ADMIN')->willReturn(true);
        $currentUser->isSuperAdmin()->willReturn(false);

        $userRepository->findByRole('ROLE_SUPER_ADMIN')->willReturn([$superAdminUser]);
        $userRepository->findAllBut([$currentUser, $superAdminUser])->willReturn([$regularUser]);

        $this->forCurrentOne()->shouldReturn([$regularUser]);
    }

    function it_returns_administrable_users_for_super_administrator(
        $currentTokenUser,
        $userRepository,
        UserInterface $currentUser,
        UserInterface $adminUser,
        UserInterface $regularUser
    ) {
        $currentTokenUser->getFromTokenStorage()->willReturn($currentUser);
        $currentUser->hasRole('ROLE_ADMIN')->willReturn(false);
        $currentUser->isSuperAdmin()->willReturn(true);

        $userRepository->findByRole(Argument::any())->shouldNotBeCalled();
        $userRepository->findAllBut([$currentUser])->willReturn([$regularUser, $adminUser]);

        $this->forCurrentOne()->shouldReturn([$regularUser, $adminUser]);
    }
}
