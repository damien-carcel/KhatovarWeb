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

namespace Khatovar\Component\User\Application\Command;

use Khatovar\Component\User\Application\Query\GetUserRoles;
use Khatovar\Component\User\Domain\Exception\UserRoleDoesNotExist;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class SetRoleHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var GetUserRoles */
    private $getUserRoles;

    public function __construct(
        UserRepositoryInterface $userRepository,
        GetUserRoles $getUserRoles
    ) {
        $this->userRepository = $userRepository;
        $this->getUserRoles = $getUserRoles;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function handle(SetRole $setRole): void
    {
        $role = $setRole->role();
        $choices = $this->getUserRoles->available();
        if (!isset($choices[$role])) {
            throw new UserRoleDoesNotExist($role);
        }

        $user = $setRole->user();
        $user->setRoles([$choices[$role]]);

        $this->userRepository->save($user);
    }
}
