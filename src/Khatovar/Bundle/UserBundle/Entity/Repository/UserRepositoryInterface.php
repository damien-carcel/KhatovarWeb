<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Entity\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User repository interface.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface UserRepositoryInterface extends ObjectRepository
{
    /**
     * Retrieves all users but one.
     *
     * @param UserInterface[] $users the users we don\'t want to be returned
     *
     * @return UserInterface[]
     */
    public function findAllBut(array $users);

    /**
     * Retrieves all users having a specific role.
     *
     * @param string $role
     *
     * @return UserInterface[]
     */
    public function findByRole($role);
}
