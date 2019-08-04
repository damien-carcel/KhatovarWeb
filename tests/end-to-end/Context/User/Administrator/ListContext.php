<?php

declare(strict_types=1);

/*
 * This file is part of Khatovar.
 *
 * Copyright (c) 2019 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Tests\EndToEnd\Context\User\Administrator;

use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;
use Khatovar\Tests\EndToEnd\Service\Assert\AssertUsersAreAdministrableOnes;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class ListContext extends UserRawContext
{
    private $assertUsersAreAdministrableOnes;

    public function __construct(AssertUsersAreAdministrableOnes $assertUsersAreAdministrableOnes)
    {
        $this->assertUsersAreAdministrableOnes = $assertUsersAreAdministrableOnes;
    }

    /**
     * @When I go on the administration page
     */
    public function goToTheAdministrationPage(): void
    {
        $this->visitPath('profile/');
        $this->page()->clickLink('Page d\'administration');
    }

    /**
     * @Then I should see the list of all the other users except the super admin
     */
    public function seeAllUsersButMyselfAndTheSuperAdmin(): void
    {
        $this->assertPageContainsText('Administration des utilisateurs');
        ($this->assertUsersAreAdministrableOnes)([
            'chips',
            'damien',
            'freya',
            'hegor',
            'lilith',
        ]);
    }

    /**
     * @Then I should see all the regular users
     */
    public function seeAllUsersButSuperAdmin(): void
    {
        $this->assertPageContainsText('Administration des utilisateurs');
        ($this->assertUsersAreAdministrableOnes)([
            'aurore',
            'chips',
            'damien',
            'freya',
            'hegor',
            'lilith',
        ]);
    }
}
