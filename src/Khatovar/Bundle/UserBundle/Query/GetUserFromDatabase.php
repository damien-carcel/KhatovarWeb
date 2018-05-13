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

namespace Khatovar\Bundle\UserBundle\Query;

use Khatovar\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use Khatovar\Bundle\UserBundle\Entity\UserInterface;
use Khatovar\Bundle\UserBundle\Security\Core\Authentication\CurrentUser;
use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class GetUserFromDatabase implements GetUser
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var CurrentUser */
    private $currentUser;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param CurrentUser             $currentUser
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        CurrentUser $currentUser
    ) {
        $this->userRepository = $userRepository;
        $this->currentUser = $currentUser;
    }

    /**
     * {@inheritdoc}
     */
    public function byUsername(string $username): UserInterface
    {
        $user = $this->userRepository->findOneByUsername($username);

        if (null === $user) {
            throw new  UserDoesNotExist($username);
        }

        if ($user->isSuperAdmin() && !$this->currentUser->isSuperAdmin()) {
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
