<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Khatovar\Bundle\UserBundle\Manager;

use Khatovar\Bundle\UserBundle\Entity\UserInterface;
use Khatovar\Bundle\UserBundle\Manager\RolesManager;
use Khatovar\Bundle\UserBundle\Security\Core\Authentication\CurrentUser;
use PhpSpec\ObjectBehavior;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class RolesManagerSpec extends ObjectBehavior
{
    function let(CurrentUser $currentUser)
    {
        $this->beConstructedWith(
            $currentUser,
            [
                'ROLE_VIEWER'      => ['ROLE_USER'],
                'ROLE_ADMIN'       => ['ROLE_VIEWER'],
                'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN'],
            ]
        );
    }

    function it_is_a_roles_manager()
    {
        $this->shouldHaveType(RolesManager::class);
    }

    function it_returns_the_list_of_roles_for_the_super_admin($currentUser)
    {
        $currentUser->isSuperAdmin()->willReturn(true);

        $this->getChoices()->shouldReturn([
            'ROLE_USER'   => 'ROLE_USER',
            'ROLE_VIEWER' => 'ROLE_VIEWER',
            'ROLE_ADMIN'  => 'ROLE_ADMIN',
        ]);
    }

    function it_returns_the_list_of_roles_for_regular_admin($currentUser)
    {
        $currentUser->isSuperAdmin()->willReturn(false);

        $this->getChoices()->shouldReturn([
            'ROLE_USER'   => 'ROLE_USER',
            'ROLE_VIEWER' => 'ROLE_VIEWER',
        ]);
    }

    function it_returns_the_role_of_a_user(UserInterface $user)
    {
        $user->getRoles()->willReturn(['ROLE_USER']);

        $this->forUser($user)->shouldReturn('ROLE_USER');
    }
}
