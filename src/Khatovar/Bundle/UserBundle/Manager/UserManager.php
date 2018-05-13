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

use Khatovar\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use Khatovar\Bundle\UserBundle\Entity\UserInterface;
use Khatovar\Bundle\UserBundle\Security\Core\Authentication\CurrentUser;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * User manager.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserManager
{
    /** @var CurrentUser */
    private $currentUser;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var RegistryInterface */
    private $doctrine;

    /** @var RolesManager */
    private $rolesManager;

    /**
     * @param CurrentUser             $currentUser
     * @param UserRepositoryInterface $userRepository
     * @param RegistryInterface       $doctrine
     * @param RolesManager            $rolesManager
     */
    public function __construct(
        CurrentUser $currentUser,
        UserRepositoryInterface $userRepository,
        RegistryInterface $doctrine,
        RolesManager $rolesManager
    ) {
        $this->currentUser = $currentUser;
        $this->userRepository = $userRepository;
        $this->doctrine = $doctrine;
        $this->rolesManager = $rolesManager;
    }

    /**
     * @return UserInterface[]
     */
    public function getAdministrableUsers()
    {
        $users = [];

        $currentUser = $this->currentUser->getFromTokenStorage();
        $users[] = $currentUser;

        if (!$currentUser->isSuperAdmin()) {
            $superAdmins = $this->userRepository->findByRole('ROLE_SUPER_ADMIN');
            $users = array_merge($users, $superAdmins);
        }

        return $this->userRepository->findAllBut($users);
    }

    /**
     * Sets a user role.
     *
     * New role is provided as an key-value array:
     * [
     *     'roles' => 'ROLE_TO_SET',
     * ]
     *
     * @param UserInterface $user
     * @param array         $selectedRole
     */
    public function setRole(UserInterface $user, array $selectedRole)
    {
        $choices = $this->rolesManager->getChoices();

        if (!isset($choices[$selectedRole['roles']])) {
            throw new InvalidArgumentException(
                sprintf('Impossible to set role %s', $selectedRole['roles'])
            );
        }

        $user->setRoles([$choices[$selectedRole['roles']]]);

        $this->doctrine->getManager()->flush();
    }
}
