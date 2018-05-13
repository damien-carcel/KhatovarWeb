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

namespace spec\Khatovar\Bundle\UserBundle\Manager;

use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Application\Query\GetUserRoles;
use Khatovar\Bundle\UserBundle\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserManagerSpec extends ObjectBehavior
{
    function let(RegistryInterface $doctrine, GetUserRoles $getUserRoles, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($doctrine, $getUserRoles, $eventDispatcher);
    }

    function it_is_a_user_manager()
    {
        $this->shouldHaveType(UserManager::class);
    }

    function it_sets_role_to_a_user(
        $getUserRoles,
        $doctrine,
        $eventDispatcher,
        EntityManagerInterface $entityManager,
        UserInterface $user
    ) {
        $getUserRoles->available()->willReturn(
            [
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_VIEWER' => 'ROLE_VIEWER',
                'ROLE_ADMIN' => 'ROLE_ADMIN',
            ]
        );


        $user->setRoles(['ROLE_ADMIN'])->shouldBeCalled();
        $doctrine->getManager()->willReturn($entityManager);
        $entityManager->flush()->shouldBeCalled();

        $eventDispatcher->dispatch(UserEvents::PRE_SET_ROLE, Argument::cetera())->shouldBeCalledTimes(1);
        $eventDispatcher->dispatch(UserEvents::POST_SET_ROLE, Argument::cetera())->shouldBeCalledTimes(1);

        $this->setRole($user, ['roles' => 'ROLE_ADMIN']);
    }

    function it_throws_an_exception_if_role_is_not_in_choices_list(
        $getUserRoles,
        $doctrine,
        $eventDispatcher,
        EntityManagerInterface $entityManager,
        UserInterface $user
    ) {
        $getUserRoles->available()->willReturn(
            [
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_VIEWER' => 'ROLE_VIEWER',
            ]
        );

        $user->setRoles([Argument::any()])->shouldNotBeCalled();
        $doctrine->getManager()->willReturn($entityManager);
        $entityManager->flush()->shouldNotBeCalled();
        $eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new InvalidArgumentException('Impossible to set role ROLE_ADMIN'))
            ->during('setRole', [$user, ['roles' => 'ROLE_ADMIN']]);
    }
}
