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
final class EditContext extends UserRawContext
{
    /**
     * @When I rename the user :originalUsername as :newUsername
     */
    public function renameUserAs(string $originalUsername, string $newUsername): void
    {
        $this->followActionLinkForUserRaw('Éditer', $originalUsername);
        $this->assertPageContainsText('Édition du profil de l\'utilisateur damien');

        $this->fillFormFieldsAndValidateWithAction([
            'Nom d\'utilisateur' => 'pandore',
            'Adresse e-mail' => 'pandore@gmail.com',
        ], 'Mettre à jour');
    }

    /**
     * @When I try to edit the super administrator profile
     */
    public function editSuperAdminProfile(): void
    {
        $this->visitPath('admin/admin/edit');
    }

    /**
     * @Then I should be notified that the user profile was updated
     */
    public function userWasUpdated(): void
    {
        $this->assertPageContainsText('Le profil utilisateur a été mis à jour');
    }

    /**
     * @Then I am forbidden to access the page
     */
    public function forbiddenToAccess(): void
    {
        $this->assertPageContainsText('403 Forbidden');
    }
}
