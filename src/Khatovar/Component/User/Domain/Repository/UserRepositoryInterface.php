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

namespace Khatovar\Component\User\Domain\Repository;

use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Khatovar\Component\User\Domain\Model\UserInterface;

/**
 * User repository interface.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface UserRepositoryInterface
{
    /**
     * Returns all users but provided ones.
     *
     * @param UserInterface[] $users the users we don\'t want to be returned
     *
     * @return UserInterface[]
     */
    public function findAllBut(array $users): array;

    /**
     * Returns all users having a specific role.
     *
     * @param string $role
     *
     * @return UserInterface[]
     */
    public function findByRole($role): array;

    /**
     * Returns a user from its username.
     *
     * @param string $username
     *
     * @throws UserDoesNotExist
     *
     * @return UserInterface|null
     */
    public function get(string $username): UserInterface;
}
