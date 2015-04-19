<?php
/**
 *
 * This file is part of KhatovarWeb.
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
 *
 * @copyright   Copyright (C) Damien Carcel (https://github.com/damien-carcel)
 * @link        https://github.com/damien-carcel/KhatovarWeb
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\PhotoBundle\Services;

use Carcel\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Class EntityList
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\PhotoBundle\Services
 */
class EntityList
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get a sorted list of all photos currently uploaded.
     *
     * @return array
     */
    public function getEntirePhotoList()
    {
        return array(
            'Photos orphelines' => $this->em
                ->getRepository('KhatovarPhotoBundle:Photo')
                ->getOrphans(),
            'Pages d\'accueil'  => $this->em
                ->getRepository('KhatovarHomepageBundle:Homepage')
                ->findAll(),
            'Membres'           => $this->em
                ->getRepository('KhatovarMemberBundle:Member')
                ->findAll(),
            'Exactions'         => $this->em
                ->getRepository('KhatovarExactionBundle:Exaction')
                ->findAll(),
        );
    }

    /**
     * Get a member's photos.
     *
     * @param User $currentUser
     *
     * @return array
     */
    public function getCurrentMemberPhotos(User $currentUser)
    {
        $member = $this->em->getRepository('KhatovarMemberBundle:Member')
            ->findOneBy(array('owner' => $currentUser));

        return array(
            'Membre :' => array(
                $member->getId() => $member
            )
        );
    }
}
