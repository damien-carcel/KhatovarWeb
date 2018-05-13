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

namespace Khatovar\Component\User\Application\Query;

use Khatovar\Component\User\Domain\Model\UserInterface;

/**
 * Query user roles.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class GetUserRoles
{
    /** @var CurrentTokenUser */
    private $currentTokenUser;

    /** @var array */
    private $roles;

    /**
     * @param CurrentTokenUser $currentUser
     * @param array            $roles
     */
    public function __construct(
        CurrentTokenUser $currentUser,
        array $roles
    ) {
        $this->currentTokenUser = $currentUser;
        $this->roles = $roles;
    }

    /**
     * Returns a list of choice for user's roles.
     *
     * @return array
     */
    public function available(): array
    {
        $choices = $this->getOrderedRoles();

        if (isset($choices['ROLE_SUPER_ADMIN'])) {
            unset($choices['ROLE_SUPER_ADMIN']);
        }

        if (isset($choices['ROLE_ADMIN']) && !$this->currentTokenUser->isSuperAdmin()) {
            unset($choices['ROLE_ADMIN']);
        }

        return $choices;
    }

    /**
     * Gets the current role of a user.
     *
     * @param UserInterface $user
     *
     * @return string
     */
    public function forUser(UserInterface $user): string
    {
        $currentRole = '';
        $userRoles = $user->getRoles();

        if (in_array($userRoles[0], $this->getOrderedRoles())) {
            $currentRole = $userRoles[0];
        }

        return $currentRole;
    }

    /**
     * Returns the list of roles, ordered by power level.
     *
     * Transform the "security.role_hierarchy.roles" parameter:
     *
     * [
     *      'ROLE_ADMIN'       => ['ROLE_USER'],
     *      'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN'],
     * ]
     *
     * into:
     *
     * [
     *     'ROLE_USER'        => 'ROLE_USER',
     *     'ROLE_ADMIN'       => 'ROLE_ADMIN',
     *     'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
     * ]
     *
     * @return array
     */
    private function getOrderedRoles(): array
    {
        $choices = [];

        foreach ($this->roles as $key => $roles) {
            foreach ($roles as $role) {
                $choices[$role] = $role;
            }

            $choices[$key] = $key;
        }

        return $choices;
    }
}
