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

use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class StatusContext extends UserRawContext
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @When I deactivate the user :username
     */
    public function deactivateUser(string $username): void
    {
        $this->visitPath('admin');
        $this->followActionLinkForUserRaw('Désactiver', $username);
    }

    /**
     * @When I activate the user :username
     */
    public function activateUser(string $username): void
    {
        $this->visitPath('admin');
        $this->followActionLinkForUserRaw('Activer', $username);
    }

    /**
     * @Then the user :username should be deactivated
     */
    public function userShouldBeDisabled(string $username): void
    {
        $this->assertPageContainsText('L\'utilisateur a été désactivé');
        $this->assertUserStatus($username, false);
    }

    /**
     * @Then the user :username should be activated
     */
    public function userShouldBeEnabled(string $username): void
    {
        $this->assertPageContainsText('L\'utilisateur a été activé');
        $this->assertUserStatus($username, true);
    }

    /**
     * @Then I cannot change the status of another administrator
     */
    public function cannotChangeAnotherAdminStatus(): void
    {
        $this->assertTableLineDoesNotContainText('hegor', 'Désactiver');
        $this->visitPath('admin/hegor/status');
        $this->assertPageContainsText('403 Forbidden');
    }

    private function assertUserStatus(string $username, bool $status): void
    {
        $user = $this->userRepository->get($username);

        Assert::true($status === $user->isEnabled());
    }
}
