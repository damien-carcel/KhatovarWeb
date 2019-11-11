<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Khatovar\Bundle\UserBundle\Repository\Doctrine;

use Khatovar\Bundle\UserBundle\Entity\User;
use Khatovar\Component\User\Domain\Exception\UserDoesNotExist;
use Khatovar\Component\User\Domain\Model\UserInterface;
use Khatovar\Component\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * User repository.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserRepository implements UserRepositoryInterface
{
    /** @var RegistryInterface */
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBut(array $users): array
    {
        $userNames = [];

        foreach ($users as $user) {
            $userNames[] = $user->getUsername();
        }

        $queryBuilder = $this->doctrine->getRepository(User::class)->createQueryBuilder('u');

        $query = $queryBuilder
            ->where($queryBuilder->expr()->notIn('u.username', $userNames))
            ->orderBy('u.username')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByRole($role): array
    {
        $queryBuilder = $this->doctrine->getRepository(User::class)->createQueryBuilder('u');

        $query = $queryBuilder
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $username): UserInterface
    {
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['username' => $username]);

        if (null === $user) {
            throw new UserDoesNotExist($username);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function save(UserInterface $user): void
    {
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(UserInterface $user): void
    {
        $this->doctrine->getManager()->remove($user);
        $this->doctrine->getManager()->flush();
    }
}
