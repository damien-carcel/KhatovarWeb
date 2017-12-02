<?php

/*
 * This file is part of CarcelUserBundle.
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
use Carcel\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use Carcel\Bundle\UserBundle\Manager\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Defines application features for authentication.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserBundleFeatureContext extends MinkContext implements KernelAwareContext
{
    /** @var KernelInterface */
    protected $kernel;

    /** @var SessionInterface */
    protected $session;

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
    public function setKernel(KernelInterface $kernel)
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
    public function iShouldBeAuthenticatedAs($username)
    {
        $tokenStorage = $this->getTockenStorage();

        if (null === $token = $tokenStorage->getToken()) {
            throw new TokenNotFoundException();
        }

        Assert::assertEquals($token->getUsername(), $username);
    }

    /**
     * Checks that user is not authenticated.
     *
     * @Given /^I am anonymous$/
     * @Then /^I should be anonymous$/
     */
    public function iShouldBeAnonymous()
    {
        $checker = $this->getAuthorizationChecker();

        Assert::assertTrue($checker->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'));
        Assert::assertFalse($checker->isGranted('ROLE_USER'));
    }

    /**
     * Resets an user password.
     *
     * @param string $username
     *
     * @Given /^I reset "(?P<username>[^"]*)" password$/
     */
    public function iResetUserPassword($username)
    {
        $user = $this->getUserProvider()->loadUserByUsername($username);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->getFosUserManager()->updateUser($user);
    }

    /**
     * Checks what users are listed in the admin page.
     *
     * @param string $list
     *
     * @Then /^I should see the users? "([^"]*)"$/
     */
    public function iShouldSeeTheFollowingUsers($list)
    {
        $providedUserNames = $this->listToArray($list);
        sort($providedUserNames);

        $storedUsers = $this->getCarcelUserManager()->getAdministrableUsers();

        $userNames = [];
        foreach ($storedUsers as $storedUser) {
            $userNames[] = $storedUser->getUsername();
        }
        sort($userNames);

        Assert::assertEquals($providedUserNames, $userNames);
    }

    /**
     * Launches an action for a specific username from a hyperlink.
     *
     * @param string $action
     * @param string $username
     *
     * @When /^I follow "(?P<action>[^"]*)" for "(?P<username>[^"]*)" profile$/
     */
    public function iFollowTheActionLinkForTheUserProfile($action, $username)
    {
        $action = $this->fixStepArgument($action);

        $row = $this->findUserRowByText($username);
        $link = $row->findLink($action);

        Assert::assertNotNull($link, 'Cannot find link in row with text '.$action);
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
    public function iPressTheActionLinkForTheUserProfile($action, $username)
    {
        $action = $this->fixStepArgument($action);

        $row = $this->findUserRowByText($username);
        $button = $row->findButton($action);

        Assert::assertNotNull($button, 'Cannot find button in row with text '.$action);
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
    public function userShouldHaveRole($username, $role)
    {
        $user = $this->getUserRepository()->findOneBy(['username' => $username]);
        Assert::assertTrue($user->hasRole($role));
    }

    /**
     * Checks that a specific table line does not contain a specific text.
     *
     * @param string $line
     * @param string $text
     *
     * @Then /^I should not see "(?P<text>[^"]*)" in the table line containing "(?P<line>[^"]*)"$/
     */
    public function iShouldNotSeeTheTextInTheTableLine($line, $text)
    {
        $element = sprintf('table tr:contains("%s")', $line);

        $this->assertElementNotContainsText($element, $text);
    }

    /**
     * Checks that a mail with a specific subject has been sent.
     *
     * @param string $subject
     *
     * @Then /^I should get a confirmation email with subject "(?P<subject>[^"]*)"$/
     */
    public function iShouldGetConfirmationEmailWithSubject($subject)
    {
        $collector = $this->getSymfonyProfile()->getCollector('swiftmailer');

        Assert::assertEquals(1, $collector->getMessageCount());

        $messages = $collector->getMessages();
        $message = $messages[0];

        Assert::assertEquals($subject, $message->getSubject());
    }

    /**
     * Disables the automatic following of redirections.
     *
     * @When /^I stop following redirections$/
     */
    public function disableFollowRedirects()
    {
        $this->getSymfonyClient()->followRedirects(false);
    }

    /**
     * Enables the automatic following of redirections.
     *
     * @When /^I start following redirections$/
     */
    public function enableFollowRedirects()
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
    public function iFollowTheActivationLinkForTheUser($username)
    {
        $user = $this->getUserRepository()->findOneBy(['username' => $username]);
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
    public function userShouldBeEnabled($username)
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
    public function userShouldBeDisabled($username)
    {
        $this->assertUserStatus($username, false);
    }

    /**
     * Asserts a user status.
     *
     * @param string $username
     * @param bool   $status
     */
    protected function assertUserStatus($username, $status)
    {
        $user = $this->getUserRepository()->findOneBy(['username' => $username]);

        Assert::assertTrue($status === $user->isEnabled());
    }

    /**
     * Gets the current Symfony profile.
     *
     * @throws \RuntimeException
     *
     * @return Profile
     */
    protected function getSymfonyProfile()
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
    protected function getSymfonyClient()
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
    protected function findUserRowByText($username)
    {
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $username));

        Assert::assertNotNull($row, 'Cannot find a table row with username '.$username);

        return $row;
    }

    /**
     * @return TokenStorageInterface
     */
    protected function getTockenStorage()
    {
        return $this->kernel->getContainer()->get('security.token_storage');
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker()
    {
        return $this->kernel->getContainer()->get('security.authorization_checker');
    }

    /**
     * @return UserProviderInterface
     */
    protected function getUserProvider()
    {
        return $this->kernel->getContainer()->get('fos_user.user_provider.username');
    }

    /**
     * @return UserManagerInterface
     */
    protected function getFosUserManager()
    {
        return $this->kernel->getContainer()->get('fos_user.user_manager');
    }

    /**
     * @return UserManager
     */
    protected function getCarcelUserManager()
    {
        return $this->kernel->getContainer()->get('carcel_user.manager.users');
    }

    /**
     * @param string $list
     *
     * @return string[]
     */
    protected function listToArray($list)
    {
        if (empty($list)) {
            return [];
        }

        return explode(', ', str_replace(' and ', ', ', $list));
    }

    /**
     * @return UserRepositoryInterface
     */
    protected function getUserRepository()
    {
        return $this->kernel->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CarcelUserBundle:User');
    }
}
