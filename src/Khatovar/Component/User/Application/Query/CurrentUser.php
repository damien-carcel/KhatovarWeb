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

/**
 * Manage the current user state.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface CurrentUser
{
    /**
     * Gets a user from the Security Token Storage.
     *
     * @return UserInterface|null
     */
    public function getFromTokenStorage(): ?UserInterface;

    /**
     * Checks that current user is super administrator or not.
     *
     * If this service is called from command line (i.e. no token), then it is
     * considered as used by a super administrator.
     *
     * However, anonymous user (a token, but no user) is not considered as a
     * super administrator.
     *
     * @return bool true if he is, false if not
     */
    public function isSuperAdmin(): bool;
}
