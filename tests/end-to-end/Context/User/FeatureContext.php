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

namespace Khatovar\Tests\EndToEnd\Context\User;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use FOS\UserBundle\Model\UserManagerInterface;
use Khatovar\Bundle\UserBundle\Repository\Doctrine\UserRepository;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Webmozart\Assert\Assert;

/**
 * Defines application features for authentication.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FeatureContext extends MinkContext implements KernelAwareContext
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
     * @return UserRepositoryInterface
     */
    private function userRepository(): UserRepositoryInterface
    {
        return $this->kernel->getContainer()->get(UserRepository::class);
    }
}
