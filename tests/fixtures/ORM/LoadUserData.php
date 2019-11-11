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

namespace Khatovar\Tests\Fixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface;
use Khatovar\Bundle\UserBundle\Entity\User;

/**
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class LoadUserData implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $usersData = $this->getUsersDataFromJson();

        foreach ($usersData as $username => $userData) {
            $user = $this->createUser($username, $userData);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @throws \Exception
     */
    private function getUsersDataFromJson(): array
    {
        $pathToJsonUsersData = __DIR__.'/../users.json';
        if (!file_exists($pathToJsonUsersData)) {
            throw new \Exception(sprintf('There is no file at path "%s"', $pathToJsonUsersData));
        }

        $jsonUsersData = file_get_contents($pathToJsonUsersData);

        return json_decode($jsonUsersData, true);
    }

    private function createUser(string $username, array $userData): UserInterface
    {
        $user = new User();

        $user->setUsername($username);
        $user->setPlainPassword($userData['password']);
        $user->setEmail($userData['email']);
        $user->setEnabled($userData['enabled']);

        if (!empty($userData['role'])) {
            $user->setRoles([$userData['role']]);
        }

        return $user;
    }
}
