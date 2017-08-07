<?php

/**
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2015 Damien Carcel (https://github.com/damien-carcel)
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

namespace Khatovar\Bundle\PhotoBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\PhotoBundle\Entity\Photo;
use Khatovar\Bundle\WebBundle\Helper\EntityHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class PhotoFactory
{
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param EntityManagerInterface        $entityManager
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * Return a new instance of Photo, with fields set according to
     * the user permissions of the creator.
     *
     * @return Photo
     */
    public function createPhoto()
    {
        $photo = new Photo();

        if (!$this->authorizationChecker->isGranted('ROLE_EDITOR')) {
            $user = $this->tokenStorage->getToken()->getUser();
            $loggedMember = $this->entityManager
                ->getRepository('KhatovarWebBundle:Member')
                ->getLoggedMember($user->getId());

            $photo
                ->setClass('none')
                ->setEntity(EntityHelper::MEMBER_CODE)
                ->setMember($loggedMember);
        }

        return $photo;
    }
}
