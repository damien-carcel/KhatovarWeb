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

namespace Khatovar\Bundle\UserBundle\Entity\Repository;

use Khatovar\Bundle\UserBundle\Entity\UserInterface;

/**
 * User repository interface.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface UserRepositoryInterface
{
    /**
     * Returns all users but provided ones.
     *
     * @param UserInterface[] $users the users we don\'t want to be returned
     *
     * @return UserInterface[]
     */
    public function findAllBut(array $users): array;

    /**
     * Returns all users having a specific role.
     *
     * @param string $role
     *
     * @return UserInterface[]
     */
    public function findByRole($role): array;

    /**
     * Returns a user from its username.
     *
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function findOneByUsername(string $username): ?UserInterface;
}
