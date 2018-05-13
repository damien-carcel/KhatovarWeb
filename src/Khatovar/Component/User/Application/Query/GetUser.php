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

namespace Khatovar\Component\User\Application\Query;

use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class GetUser
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var CurrentTokenUser */
    private $currentTokenUser;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param CurrentTokenUser        $currentUser
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        CurrentTokenUser $currentUser
    ) {
        $this->userRepository = $userRepository;
        $this->currentTokenUser = $currentUser;
    }

    /**
     * Returns a User from its username.
     *
     * A regular administrator cannot get the super administrator, as he has no right to access its profile.
     *
     * @param string $username
     *
     * @throws UserDoesNotExist
     * @throws AccessDeniedException
     *
     * @return UserInterface
     */
    public function byUsername(string $username): UserInterface
    {
        $user = $this->userRepository->get($username);

        if ($user->isSuperAdmin() && !$this->currentTokenUser->isSuperAdmin()) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have the permission to get user "%s".',
                    $username
                )
            );
        }

        return $user;
    }
}
