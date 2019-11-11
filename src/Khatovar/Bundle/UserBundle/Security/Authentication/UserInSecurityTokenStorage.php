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

namespace Khatovar\Bundle\UserBundle\Security\Authentication;

use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Implementation of current user state using the Symfony security token storage.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserInSecurityTokenStorage implements CurrentTokenUser
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromTokenStorage(): ?UserInterface
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin(): bool
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return true;
        }

        if (!is_object($user = $token->getUser())) {
            return false;
        }

        return $user->isSuperAdmin();
    }
}
