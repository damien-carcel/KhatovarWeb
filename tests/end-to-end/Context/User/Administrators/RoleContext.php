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

use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Khatovar\Tests\EndToEnd\Context\User\UserRawContext;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
final class RoleContext extends UserRawContext
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @When /^I change the role of (?P<username>[^"]*) for "(?P<role>[^"]*)"$/
     */
    public function changeTheUserRole(string $username, string $role): void
    {
        $this->followActionLinkForUserRaw('Changer le rôle', $username);
        $this->page()->selectFieldOption('Rôles', $role);
        $this->page()->pressButton('Modifier');
    }

    /**
     * @When I try to change :username role
     */
    public function tryToChangeUserRole(string $username): void
    {
        $this->followActionLinkForUserRaw('Changer le rôle', $username);
    }

    /**
     * @Then the user :username should be a :role
     */
    public function userShouldHaveRole(string $username, string $role): void
    {
        $this->assertPageContainsText('Le rôle de l\'utilisateur a été modifié');

        $user = $this->userRepository->get($username);
        $roleCode = sprintf(
            'ROLE_%s',
            strtoupper($role)
        );

        Assert::true($user->hasRole($roleCode));
    }

    /**
     * @Then It cannot be promoted to administrator
     */
    public function cannotPromoteUserToAdmin(): void
    {
        $this->assertElementContainsText('select', 'Utilisateur basique');
        $this->assertElementContainsText('select', 'Lecture seule');
        $this->assertElementNotContainsText('select', 'Administrateur');
        $this->assertElementNotContainsText('select', 'Super Administrateur');
    }

    /**
     * @Then I cannot demote another administrator
     */
    public function cannotDemoteAnotherAdmin(): void
    {
        $this->assertElementNotContainsText(
            sprintf(
                'table tr:contains("%s")',
                'hegor'
            ),
            'Changer le rôle'
        );

        $this->visitPath('admin/hegor/role');
        $this->assertPageContainsText('403 Forbidden');
    }
}
