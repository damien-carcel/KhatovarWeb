<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Khatovar\Component\User\Application\Query;

use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Component\User\Application\Query\GetUserRoles;
use Khatovar\Component\User\Domain\Model\UserInterface;
use PhpSpec\ObjectBehavior;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class GetUserRolesSpec extends ObjectBehavior
{
    function let(CurrentTokenUser $currentTokenUser)
    {
        $this->beConstructedWith(
            $currentTokenUser,
            [
                'ROLE_VIEWER' => ['ROLE_USER'],
                'ROLE_ADMIN' => ['ROLE_VIEWER'],
                'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN'],
            ]
        );
    }

    function it_is_a_roles_manager()
    {
        $this->shouldHaveType(GetUserRoles::class);
    }

    function it_returns_the_list_of_roles_for_the_super_admin($currentTokenUser)
    {
        $currentTokenUser->isSuperAdmin()->willReturn(true);

        $this->available()->shouldReturn([
            'ROLE_USER' => 'ROLE_USER',
            'ROLE_VIEWER' => 'ROLE_VIEWER',
            'ROLE_ADMIN' => 'ROLE_ADMIN',
        ]);
    }

    function it_returns_the_list_of_roles_for_regular_admin($currentTokenUser)
    {
        $currentTokenUser->isSuperAdmin()->willReturn(false);

        $this->available()->shouldReturn([
            'ROLE_USER' => 'ROLE_USER',
            'ROLE_VIEWER' => 'ROLE_VIEWER',
        ]);
    }

    function it_returns_the_role_of_a_user(UserInterface $user)
    {
        $user->getRoles()->willReturn(['ROLE_USER']);

        $this->forUser($user)->shouldReturn('ROLE_USER');
    }
}
