<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2018 Damien Carcel <damien.carcel@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace spec\Khatovar\Component\User\Application\Command;

use Khatovar\Bundle\UserBundle\Entity\User;
use Khatovar\Component\User\Application\Command\SetRole;
use Khatovar\Component\User\Application\Command\SetRoleHandler;
use Khatovar\Component\User\Domain\Exception\UserRoleDoesNotExist;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Application\Query\GetUserRoles;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class SetRoleHandlerSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, GetUserRoles $getUserRoles)
    {
        $this->beConstructedWith($userRepository, $getUserRoles);
    }

    function it_is_a_set_role_handler()
    {
        $this->shouldHaveType(SetRoleHandler::class);
    }

    function it_sets_role_to_a_user($userRepository, $getUserRoles)
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $setRole = new SetRole($user, 'ROLE_ADMIN');

        $getUserRoles->available()->willReturn(
            [
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_VIEWER' => 'ROLE_VIEWER',
                'ROLE_ADMIN' => 'ROLE_ADMIN',
            ]
        );

        $userRepository->save($user)->shouldBeCalled();

        $this->handle($setRole);
    }

    function it_throws_an_exception_if_role_is_not_in_choices_list($userRepository, $getUserRoles)
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $setRole = new SetRole($user, 'ROLE_ADMIN');

        $getUserRoles->available()->willReturn(
            [
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_VIEWER' => 'ROLE_VIEWER',
            ]
        );

        $userRepository->save(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new UserRoleDoesNotExist('ROLE_ADMIN'))
            ->during('handle', [$setRole]);
    }
}
