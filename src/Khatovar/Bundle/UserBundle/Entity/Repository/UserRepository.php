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

use Khatovar\Bundle\UserBundle\Entity\User;
use Khatovar\Bundle\UserBundle\Entity\UserInterface;
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

    /**
     * @param RegistryInterface $doctrine
     */
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
    public function findOneByUsername(string $username): UserInterface
    {
        return $this->doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
    }
}
