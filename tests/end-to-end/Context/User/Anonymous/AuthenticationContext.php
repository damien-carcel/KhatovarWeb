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

namespace Khatovar\Tests\EndToEnd\Context\User\Anonymous;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class AuthenticationContext extends UserRawContext implements KernelAwareContext
{
    /** @var KernelInterface */
    private $kernel;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given I am logged as :username
     */
    public function iAmLoggedAs(string $username): void
    {
        $this->visitPath('login');
        $this->logAsUserWithPassword($username, $username);
    }

    /**
     * @Given I am anonymously on the homepage
     */
    public function iAmAnonymouslyOnTheHomePage(): void
    {
        $this->visitPath('/');

        $this->assertUserIsAnonymous();
    }

    /**
     * @Given I am anonymously on the login page
     */
    public function iAmAnonymouslyOnTheLoginPage(): void
    {
        $this->visitPath('login');

        $this->assertUserIsAnonymous();
    }

    /**
     * @When I log in as :username
     */
    public function iLogInAs(string $username): void
    {
        $this->visitPath('login');
        $this->logAsUserWithPassword($username, $username);
    }

    /**
     * @When I log in then out
     */
    public function iLogInThenOut(): void
    {
        $this->visitPath('login');
        $this->logAsUserWithPassword('admin', 'admin');

        $this->visitPath('profile/');
        $this->page()->clickLink('Déconnexion');
    }

    /**
     * @When I try to go on my profile
     */
    public function iTryToGoOnMyProfile(): void
    {
        $this->visitPath('profile/');
    }

    /**
     * @Then I should be anonymous
     */
    public function iShouldBeAnonymous(): void
    {
        $this->assertUserIsAnonymous();
    }

    /**
     * @Then I should be authenticated as :username
     */
    public function iShouldBeAuthenticatedAs(string $username): void
    {
        if (null === $token = $this->tokenStorage()->getToken()) {
            throw new TokenNotFoundException();
        }

        Assert::same($token->getUsername(), $username);
    }

    /**
     * @Then I am proposed with the login screen instead
     */
    public function iAmProposedWithTheLoginScreen(): void
    {
        $this->assertPath('profile/');
        $this->assertSession()->elementContains('css', 'div', 'class="login-wall panel panel-default"');
        $this->assertPageContainsText('Se souvenir de moi');
    }

    private function assertUserIsAnonymous(): void
    {
        Assert::true($this->authorizationChecker()->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'));
        Assert::false($this->authorizationChecker()->isGranted('ROLE_USER'));
    }

    private function tokenStorage(): TokenStorageInterface
    {
        return $this->kernel->getContainer()->get('security.token_storage');
    }

    private function authorizationChecker(): AuthorizationCheckerInterface
    {
        return $this->kernel->getContainer()->get('security.authorization_checker');
    }
}
