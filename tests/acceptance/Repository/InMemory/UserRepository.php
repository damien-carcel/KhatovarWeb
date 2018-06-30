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

namespace Khatovar\Tests\Acceptance\Repository\InMemory;

use Doctrine\Common\Collections\ArrayCollection;
use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserRepository implements UserRepositoryInterface
{
    /** @var ArrayCollection */
    public $users;

    /**
     * @param UserInterface[] $users
     */
    public function __construct(array $users)
    {
        $this->users = new ArrayCollection($users);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBut(array $users): array
    {
        $keptUsers = $this->users->filter(function (UserInterface $user) use ($users) {
            return !in_array($user, $users);
        });

        return $keptUsers->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findByRole($role): array
    {
        $keptUsers = $this->users->filter(function (UserInterface $user) use ($role) {
            return in_array($role, $user->getRoles());
        });

        return $keptUsers->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $username): UserInterface
    {
        foreach ($this->users as $user) {
            if ($username === $user->getUsername()) {
                return $user;
            }
        }

        throw new UserDoesNotExist($username);
    }

    /**
     * {@inheritdoc}
     */
    public function save(UserInterface $user): void
    {
        $this->users->set($user->getUsername(), $user);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(UserInterface $user): void
    {
        $this->users->removeElement($user);
    }
}
