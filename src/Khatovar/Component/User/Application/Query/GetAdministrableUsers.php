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

use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class GetAdministrableUsers
{
    /** @var CurrentTokenUser */
    private $currentTokenUser;

    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(CurrentTokenUser $currentTokenUser, UserRepositoryInterface $userRepository)
    {
        $this->currentTokenUser = $currentTokenUser;
        $this->userRepository = $userRepository;
    }

    /**
     * Returns the list of user that the current user can administrate.
     *
     * Only administrators can administrate other users, and only a super
     * administrator can administrate other super administrators.
     *
     * This method makes sense only of there is a use in the token storage.
     *
     * @return UserInterface[]
     */
    public function forCurrentOne(): array
    {
        $currentUser = $this->currentTokenUser->getFromTokenStorage();
        if (null === $currentUser || !($currentUser->hasRole('ROLE_ADMIN') || $currentUser->isSuperAdmin())) {
            return [];
        }

        $users = [$currentUser];

        if (!$currentUser->isSuperAdmin()) {
            $superAdmins = $this->userRepository->findByRole('ROLE_SUPER_ADMIN');
            $users = array_merge($users, $superAdmins);
        }

        return $this->userRepository->findAllBut($users);
    }
}
