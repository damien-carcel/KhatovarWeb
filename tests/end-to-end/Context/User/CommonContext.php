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

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Khatovar\Component\User\Application\Query\GetAdministrableUsers;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class CommonContext extends UserRawContext implements KernelAwareContext
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
     * @When I get back to the previous page
     */
    public function backToThePreviousPage(): void
    {
        $this->page()->clickLink('Retour');
    }

    /**
     * @Then I should be on the login page
     */
    public function shouldBeOnTheLoginPage(): void
    {
        $this->assertPath('login');
    }

    /**
     * @Then /^I should see the users? "([^"]*)"$/
     */
    public function iShouldSeeTheUsers(string $list): void
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

    private function listToArray($list): array
    {
        if (empty($list)) {
            return [];
        }

        return explode(', ', str_replace(' and ', ', ', $list));
    }

    private function getAdministrableUsers(): GetAdministrableUsers
    {
        return $this->kernel->getContainer()->get(GetAdministrableUsers::class);
    }
}
