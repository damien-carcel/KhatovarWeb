<?php

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

namespace Khatovar\Bundle\UserBundle\Manager;

use Khatovar\Component\User\Application\Query\GetUserRoles;
use Khatovar\Component\User\Domain\Event\UserEvents;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * User manager.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserManager
{
    /** @var RegistryInterface */
    private $doctrine;

    /** @var GetUserRoles */
    private $getUserRoles;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param RegistryInterface        $doctrine
     * @param GetUserRoles             $getUserRoles
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        RegistryInterface $doctrine,
        GetUserRoles $getUserRoles,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->doctrine = $doctrine;
        $this->getUserRoles = $getUserRoles;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Sets a user role.
     *
     * New role is provided as an key-value array:
     * [
     *     'roles' => 'ROLE_TO_SET',
     * ]
     *
     * @param UserInterface $user
     * @param array         $selectedRole
     */
    public function setRole(UserInterface $user, array $selectedRole)
    {
        $choices = $this->getUserRoles->available();

        if (!isset($choices[$selectedRole['roles']])) {
            throw new InvalidArgumentException(
                sprintf('Impossible to set role %s', $selectedRole['roles'])
            );
        }

        $this->eventDispatcher->dispatch(UserEvents::PRE_SET_ROLE, new GenericEvent($user));

        $user->setRoles([$choices[$selectedRole['roles']]]);

        $this->doctrine->getManager()->flush();

        $this->eventDispatcher->dispatch(UserEvents::POST_SET_ROLE, new GenericEvent($user));
    }
}
