<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Context;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Driver\KernelDriver;
use FOS\UserBundle\Model\UserManagerInterface;
use Khatovar\Component\User\Application\Query\GetAdministrableUsers;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Webmozart\Assert\Assert;

/**
 * Defines application features for authentication.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserBundleFeatureContext extends MinkContext implements KernelAwareContext
{
    /** @var KernelInterface */
    private $kernel;

    /** @var SessionInterface */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * Checks that user is authenticated.
     *
     * @param string $username
     *
     * @Then /^I should be authenticated as "(?P<username>[^"]*)"$/
     */
    public function iShouldBeAuthenticatedAs($username): void
    {
        $tokenStorage = $this->getTockenStorage();

        if (null === $token = $tokenStorage->getToken()) {
            throw new TokenNotFoundException();
        }

        Assert::same($token->getUsername(), $username);
    }

    /**
     * Checks that user is not authenticated.
     *
     * @Given /^I am anonymous$/
     * @Then /^I should be anonymous$/
     */
    public function iShouldBeAnonymous(): void
    {
        $checker = $this->getAuthorizationChecker();

        Assert::true($checker->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'));
        Assert::false($checker->isGranted('ROLE_USER'));
    }

    /**
     * Resets an user password.
     *
     * @param string $username
     *
     * @Given /^I reset "(?P<username>[^"]*)" password$/
     */
    public function iResetUserPassword($username): void
    {
        $user = $this->getUserProvider()->loadUserByUsername($username);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->fosUserManager()->updateUser($user);
    }

    /**
     * Checks what users are listed in the admin page.
     *
     * @param string $list
     *
     * @Then /^I should see the users? "([^"]*)"$/
     */
    public function iShouldSeeTheFollowingUsers($list): void
    {
        $providedUserNames = $this->listToArray($list);
        sort($providedUserNames);

        $storedUsers = $this->getAdministrableUsers()->forCurrentOne();

        $userNames = [];
        foreach ($storedUsers as $storedUser) {
            $userNames[] = $storedUser->getUsername();
        }
        sort($userNames);

        Assert::same($providedUserNames, $userNames);
    }

    /**
     * Launches an action for a specific username from a hyperlink.
     *
     * @param string $action
     * @param string $username
     *
     * @When /^I follow "(?P<action>[^"]*)" for "(?P<username>[^"]*)" profile$/
     */
    public function iFollowTheActionLinkForTheUserProfile($action, $username): void
    {
        $action = $this->fixStepArgument($action);

        $row = $this->findUserRowByText($username);
        $link = $row->findLink($action);

        Assert::notNull($link, 'Cannot find link in row with text '.$action);
        $link->click();
    }

    /**
     * Launches an action for a specific username from an input buttons.
     *
     * @param string $action
     * @param string $username
     *
     * @When /^I press "(?P<action>[^"]*)" for "(?P<user>[^"]*)" profile$/
     */
    public function iPressTheActionLinkForTheUserProfile($action, $username): void
    {
        $action = $this->fixStepArgument($action);

        $row = $this->findUserRowByText($username);
        $button = $row->findButton($action);

        Assert::notNull($button, 'Cannot find button in row with text '.$action);
        $button->press();
    }

    /**
     * Checks that a user have a defined role.
     *
     * @param string $username
     * @param string $role
     *
     * @Then /^user "(?P<username>[^"]*)" should have role "(?P<role>[^"]*)"$/
     */
    public function userShouldHaveRole($username, $role): void
    {
        $user = $this->userRepository()->get($username);
        Assert::true($user->hasRole($role));
    }

    /**
     * Checks that a specific table line does not contain a specific text.
     *
     * @param string $line
     * @param string $text
     *
     * @Then /^I should not see "(?P<text>[^"]*)" in the table line containing "(?P<line>[^"]*)"$/
     */
    public function iShouldNotSeeTheTextInTheTableLine($line, $text): void
    {
        $element = sprintf('table tr:contains("%s")', $line);

        $this->assertElementNotContainsText($element, $text);
    }

    /**
     * Checks that a mail with a specific subject has been sent.
     *
     * @param string $subject
     *
     * @throws DriverException
     *
     * @Then /^I should get a confirmation email with subject "(?P<subject>[^"]*)"$/
     */
    public function iShouldGetConfirmationEmailWithSubject($subject): void
    {
        $collector = $this->getSymfonyProfile()->getCollector('swiftmailer');

        Assert::same(1, $collector->getMessageCount());

        $messages = $collector->getMessages();
        $message = $messages[0];

        Assert::same($subject, $message->getSubject());
    }

    /**
     * Disables the automatic following of redirections.
     *
     * @throws DriverException
     *
     * @When /^I stop following redirections$/
     */
    public function disableFollowRedirects(): void
    {
        $this->getSymfonyClient()->followRedirects(false);
    }

    /**
     * Enables the automatic following of redirections.
     *
     * @throws DriverException
     *
     * @When /^I start following redirections$/
     */
    public function enableFollowRedirects(): void
    {
        $this->getSymfonyClient()->followRedirects(true);
    }

    /**
     * Activates a user thanks to its activation token.
     *
     * @param string $username
     *
     * @When /^I follow the activation link for the user "(?P<username>[^"]*)"$/
     */
    public function iFollowTheActivationLinkForTheUser($username): void
    {
        $user = $this->userRepository()->get($username);
        $activationToken = $user->getConfirmationToken();

        $this->visitPath('register/confirm/'.$activationToken);
    }

    /**
     * Checks that a user is active.
     *
     * @param string $username
     *
     * @Then /^user "(?P<username>[^"]*)" should be enabled$/
     */
    public function userShouldBeEnabled($username): void
    {
        $this->assertUserStatus($username, true);
    }

    /**
     * Checks that a user is unactive.
     *
     * @param string $username
     *
     * @Then /^user "(?P<username>[^"]*)" should be disabled$/
     */
    public function userShouldBeDisabled($username): void
    {
        $this->assertUserStatus($username, false);
    }

    /**
     * Asserts a user status.
     *
     * @param string $username
     * @param bool   $status
     */
    private function assertUserStatus($username, $status): void
    {
        $user = $this->userRepository()->get($username);

        Assert::true($status === $user->isEnabled());
    }

    /**
     * Gets the current Symfony profile.
     *
     * @throws \RuntimeException
     * @throws DriverException
     *
     * @return Profile
     */
    private function getSymfonyProfile(): Profile
    {
        $profile = $this->getSymfonyClient()->getProfile();
        if (false === $profile) {
            throw new \RuntimeException('No profile associated with the current client response');
        }

        return $profile;
    }

    /**
     * Gets the current Symfony cient.
     *
     * @throws DriverException
     *
     * @return Client
     */
    private function getSymfonyClient(): Client
    {
        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof KernelDriver) {
            throw new DriverException(sprintf(
                'Expects driver to be an instance of %s',
                KernelDriver::class
            ));
        }

        return $driver->getClient();
    }

    /**
     * Finds a table row according to its content.
     *
     * @param $username
     *
     * @return NodeElement
     */
    private function findUserRowByText($username): NodeElement
    {
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $username));

        Assert::notNull($row, 'Cannot find a table row with username '.$username);

        return $row;
    }

    /**
     * @param string $list
     *
     * @return string[]
     */
    private function listToArray($list): array
    {
        if (empty($list)) {
            return [];
        }

        return explode(', ', str_replace(' and ', ', ', $list));
    }

    /**
     * @return TokenStorageInterface
     */
    private function getTockenStorage(): TokenStorageInterface
    {
        return $this->kernel->getContainer()->get('security.token_storage');
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    private function getAuthorizationChecker(): AuthorizationCheckerInterface
    {
        return $this->kernel->getContainer()->get('security.authorization_checker');
    }

    /**
     * @return UserProviderInterface
     */
    private function getUserProvider(): UserProviderInterface
    {
        return $this->kernel->getContainer()->get('fos_user.user_provider.username');
    }

    /**
     * @return UserManagerInterface
     */
    private function fosUserManager(): UserManagerInterface
    {
        return $this->kernel->getContainer()->get('fos_user.user_manager');
    }

    /**
     * @return GetAdministrableUsers
     */
    private function getAdministrableUsers(): GetAdministrableUsers
    {
        return $this->kernel->getContainer()->get('Khatovar\Component\User\Application\Query\GetAdministrableUsers');
    }

    /**
     * @return UserRepositoryInterface
     */
    private function userRepository(): UserRepositoryInterface
    {
        return $this->kernel->getContainer()->get('Khatovar\Bundle\UserBundle\Entity\Repository\UserRepository');
    }
}
