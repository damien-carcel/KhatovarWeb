<?php

/*
 * This file is part of CarcelUserBundle.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Context\DataFixtures\ORM;

use Carcel\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class LoadUserData implements FixtureInterface
{
    /** @var array */
    protected static $userData = [
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
            'role' => '',
            'enabled' => true,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (static::$userData as $data) {
            $user = $this->createUser($data);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @param array $userData
     *
     * @return User
     */
    protected function createUser(array $userData)
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
