<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel (https://github.com/damien-carcel)
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

namespace Context\DataFixtures\ORM;

use Carcel\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface;

/**
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class LoadUserData implements FixtureInterface
{
    /** @const array */
    private const USER_DATA = [
        [
            'username' => 'admin',
            'password' => 'admin',
            'email' => 'admin@documents.bundle',
            'role' => 'ROLE_SUPER_ADMIN',
            'enabled' => true,
        ],
        [
            'username' => 'aurore',
            'password' => 'aurore',
            'email' => 'aurore@documents.bundle',
            'role' => 'ROLE_ADMIN',
            'enabled' => true,
        ],
        [
            'username' => 'freya',
            'password' => 'freya',
            'email' => 'freya@documents.bundle',
            'role' => 'ROLE_UPLOADER',
            'enabled' => true,
        ],
        [
            'username' => 'lilith',
            'password' => 'lilith',
            'email' => 'lilith@documents.bundle',
            'role' => 'ROLE_VIEWER',
            'enabled' => true,
        ],
        [
            'username' => 'damien',
            'password' => 'damien',
            'email' => 'damien@documents.bundle',
            'role' => '',
            'enabled' => true,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        foreach (static::USER_DATA as $data) {
            $user = $this->createUser($data);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @param array $userData
     *
     * @return UserInterface
     */
    private function createUser(array $userData): UserInterface
    {
        $user = new User();

        $user->setUsername($userData['username']);
        $user->setPlainPassword($userData['password']);
        $user->setEmail($userData['email']);
        $user->setEnabled($userData['enabled']);

        if (!empty($userData['role'])) {
            $user->setRoles([$userData['role']]);
        }

        return $user;
    }
}
