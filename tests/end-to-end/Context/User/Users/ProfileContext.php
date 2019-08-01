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

namespace Khatovar\Tests\EndToEnd\Context\User\Users;

use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class ProfileContext extends UserRawContext
{
    /**
     * @When I go to my user profile
     */
    public function iGoToMyUserProfile(): void
    {
        $this->visitPath('profile/');
        $this->page()->clickLink('damien');
    }

    /**
     * @Then I can see my user information
     */
    public function iCanSeeMyUserInformation(): void
    {
        $this->assertPageContainsText('Profil de l\'utilisateur damien');
        $this->assertPageContainsText('Connecté en tant que damien');
    }

    /**
     * @When I change my username and my email
     */
    public function iChangeMyUsernameAndEmail(): void
    {
        $this->editProfile();
        $this->fillFormFieldsAndValidateWithAction([
            'Nom d\'utilisateur' => 'pandore',
            'Adresse e-mail' => 'pandore@khatovar.fr',
            'Mot de passe actuel' => 'damien',
        ], 'Mettre à jour');
    }

    /**
     * @Then my user information are updated
     */
    public function myUserInformationAreUpdated(): void
    {
        $this->assertPageContainsText('Le profil a été mis à jour');
        $this->assertPageContainsText('Nom d\'utilisateur: pandore');
        $this->assertPageContainsText('Adresse e-mail: pandore@khatovar.fr');
    }

    /**
     * @When I try to edit my profile with a wrong password
     */
    public function iTryToEditMyProfileWithoutProvidingMyPassword(): void
    {
        $this->editProfile();
        $this->fillFormFieldsAndValidateWithAction([
            'Nom d\'utilisateur' => 'pandore',
            'Mot de passe actuel' => 'pandore',
        ], 'Mettre à jour');
    }

    /**
     * @When I try to change my password without knowing it
     */
    public function iTryToChangeMyPasswordWithoutKnowingIt(): void
    {
        $this->editPassword();
        $this->fillFormFieldsAndValidateWithAction([
            'Mot de passe actuel' => 'wrongpassword',
            'Nouveau mot de passe' => 'pandore',
            'Répéter le nouveau mot de passe' => 'pandore',
        ], 'Modifier le mot de passe');
    }

    /**
     * @Then I am noticed that the password is invalid
     */
    public function iAmNoticedThatThePasswordIsInvalid(): void
    {
        $this->assertPageContainsText('Le mot de passe est invalide.');
    }

    /**
     * @When I change my password
     */
    public function iChangeMyPassword(): void
    {
        $this->editPassword();
        $this->fillFormFieldsAndValidateWithAction([
            'Mot de passe actuel' => 'damien',
            'Nouveau mot de passe' => 'pandore',
            'Répéter le nouveau mot de passe' => 'pandore',
        ], 'Modifier le mot de passe');
    }

    /**
     * @Then my password is changed
     */
    public function myPasswordIsChanged(): void
    {
        $this->assertPageContainsText('Le mot de passe a été modifié');
        $this->assertCanReconnectWithNewPassword();
    }

    /**
     * @When I change my password without confirming it
     */
    public function iChangeMyPasswordWithoutConfirmingIt(): void
    {
        $this->editPassword();
        $this->fillFormFieldsAndValidateWithAction([
            'Mot de passe actuel' => 'damien',
            'Nouveau mot de passe' => 'pandore',
            'Répéter le nouveau mot de passe' => 'pandora',
        ], 'Modifier le mot de passe');
    }

    /**
     * @Then I am noticed that the two passwords are different
     */
    public function iAmNoticedThatTheTwoPasswordsAreDifferent(): void
    {
        $this->assertPageContainsText('Les deux mots de passe ne sont pas identiques');
    }

    private function editProfile(): void
    {
        $this->visitPath('profile/');
        $this->page()->clickLink('Éditer le profil');

        $this->assertPath('profile/edit');
    }

    private function editPassword(): void
    {
        $this->visitPath('profile/');
        $this->page()->clickLink('Changer le mot de passe');

        $this->assertPath('profile/change-password');
    }

    private function assertCanReconnectWithNewPassword(): void
    {
        $this->page()->clickLink('Déconnexion');
        $this->logAsUserWithPassword('damien', 'pandore');

        $this->assertPath('profile/');
        $this->assertPageContainsText('Nom d\'utilisateur: damien');
    }
}
