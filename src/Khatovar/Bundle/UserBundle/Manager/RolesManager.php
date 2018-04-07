<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Manager;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * User roles manager.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class RolesManager
{
    /** @var array */
    protected $roles;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param string[]              $roles
     */
    public function __construct(TokenStorageInterface $tokenStorage, array $roles)
    {
        $this->tokenStorage = $tokenStorage;
        $this->roles = $roles;
    }

    /**
     * Returns a list of choice for user's roles.
     *
     * @return array
     */
    public function getChoices()
    {
        $choices = $this->getOrderedRoles();

        if (isset($choices['ROLE_SUPER_ADMIN'])) {
            unset($choices['ROLE_SUPER_ADMIN']);
        }

        if (isset($choices['ROLE_ADMIN']) && !$this->isCurrentUserSuperAdmin()) {
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
    public function getUserRole(UserInterface $user)
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
     * @return string[]
     */
    protected function getOrderedRoles()
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

    /**
     * Checks that current user is super administrator or not.
     *
     * If this manager is used from command line (i.e. no token), then it is
     * considered as used by a super administrator.
     * Anonymous user, however, is not considered as a super administrator.
     *
     * @return bool true if he is, false if not
     */
    protected function isCurrentUserSuperAdmin()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return true;
        }

        if (!is_object($user = $token->getUser())) {
            return false;
        }

        return $user->isSuperAdmin();
    }
}
