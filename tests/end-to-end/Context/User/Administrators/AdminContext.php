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

namespace Khatovar\Tests\EndToEnd\Context\User\Administrators;

use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class AdminContext extends UserRawContext
{
    /**
     * @Given I am logged as an administrator
     */
    public function iAmLoggedAsAnAdmin(): void
    {
        $this->visitPath('login');

        $this->fillFormFieldsAndValidateWithAction([
            'Nom d\'utilisateur' => 'aurore',
            'Mot de passe' => 'aurore',
        ], 'Connexion');
    }

    /**
     * @Given I am on the administration page
     * @Given I try to access the administration page
     */
    public function iAmOnTheAdminPage(): void
    {
        $this->visitPath('admin');
    }

    /**
     * @Then I am forbidden to access the page
     */
    public function forbiddenToAccess(): void
    {
        $this->assertPageContainsText('403 Forbidden');
    }
}
