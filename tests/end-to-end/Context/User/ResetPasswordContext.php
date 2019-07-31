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

namespace Khatovar\Tests\EndToEnd\Context\User;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class ResetPasswordContext extends UserRawContext
{
    private const EMAIL = 'damien@khatovar.fr';
    private const USERNAME = 'damien';

    private $resetWith;

    /**
     * @When I reset a password using the user username
     */
    public function resetPasswordUsingUserUsername(): void
    {
        $this->resetWith = 'username';

        $this->resetPassword(static::USERNAME);
    }

    /**
     * @When I reset a password using the user email
     */
    public function resetPasswordUsingUserEmail(): void
    {
        $this->resetWith = 'email';

        $this->resetPassword(static::EMAIL);
    }

    /**
     * @Then the password should be reset
     */
    public function passwordShouldBeReset(): void
    {
        $emailOrUsername = 'username' === $this->resetWith ? static::USERNAME : static::EMAIL;

        $this->assertPath(sprintf(
            'resetting/check-email?username=%s',
            $emailOrUsername
        ));

        $this->assertPageContainsText(
            'Un e-mail a été envoyé. Il contient un lien sur lequel il vous faudra cliquer pour réinitialiser votre mot de passe.'
        );
    }

    private function resetPassword(string $userEmailOrUserName): void
    {
        $this->page()->clickLink('Mot de passe oublié ?');
        $this->assertPath('resetting/request');

        $this->page()->fillField('Nom d\'utilisateur ou adresse e-mail', $userEmailOrUserName);
        $this->page()->pressButton('Réinitialiser le mot de passe');
    }
}
