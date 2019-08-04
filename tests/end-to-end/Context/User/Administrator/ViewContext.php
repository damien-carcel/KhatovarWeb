<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2019 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Tests\EndToEnd\Context\User\Administrator;

use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ViewContext extends UserRawContext
{
    /**
     * @When I try to access :username profile
     */
    public function tryToAccessUserProfile(string $username): void
    {
        $this->visitPath('admin');
        $this->followActionLinkForUserRaw('Visualiser', $username);
    }

    /**
     * @When I try to access the profile of the super administrator
     */
    public function tryToAccessSuperAdminProfile(): void
    {
        $this->visitPath('admin/admin/show');
    }

    /**
     * @Then I can see the profile of :username
     */
    public function canSeeUserProfile(string $username): void
    {
        $this->assertPageContainsText(sprintf('Profil de l\'utilisateur %s', $username));
        $this->assertPageContainsText(sprintf('Nom d\'utilisateur: %s', $username));
        $this->assertPageContainsText(sprintf('Adresse e-mail: %s@khatovar.fr', $username));
    }
}
