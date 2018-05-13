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

use Khatovar\Component\User\Application\Query\CurrentTokenUser;
use Khatovar\Component\User\Application\Query\UserRole;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * User manager.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserManager
{
    /** @var CurrentTokenUser */
    private $currentTokenUser;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var RegistryInterface */
    private $doctrine;

    /** @var UserRole */
    private $userRole;

    /**
     * @param CurrentTokenUser        $currentUser
     * @param UserRepositoryInterface $userRepository
     * @param RegistryInterface       $doctrine
     * @param UserRole                $userRole
     */
    public function __construct(
        CurrentTokenUser $currentUser,
        UserRepositoryInterface $userRepository,
        RegistryInterface $doctrine,
        UserRole $userRole
    ) {
        $this->currentTokenUser = $currentUser;
        $this->userRepository = $userRepository;
        $this->doctrine = $doctrine;
        $this->userRole = $userRole;
    }

    /**
     * @return UserInterface[]
     */
    public function getAdministrableUsers()
    {
        $users = [];

        $currentUser = $this->currentTokenUser->getFromTokenStorage();
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
        $choices = $this->userRole->listAvailableOnes();

        if (!isset($choices[$selectedRole['roles']])) {
            throw new InvalidArgumentException(
                sprintf('Impossible to set role %s', $selectedRole['roles'])
            );
        }

        $user->setRoles([$choices[$selectedRole['roles']]]);

        $this->doctrine->getManager()->flush();
    }
}
