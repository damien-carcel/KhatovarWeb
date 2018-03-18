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

use Doctrine\ORM\EntityRepository;

/**
 * User repository.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAllBut(array $users)
    {
        $userNames = [];

        foreach ($users as $user) {
            $userNames[] = $user->getUsername();
        }

        $queryBuilder = $this->createQueryBuilder('u');

        $query = $queryBuilder
            ->where($queryBuilder->expr()->notIn('u.username', $userNames))
            ->orderBy('u.username')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByRole($role)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
            ->getQuery();

        return $query->getResult();
    }
}
