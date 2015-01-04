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
 * Class PhotosSide
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\PhotoBundle\Services
 */
class PhotosSide
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
     * Return the list of all photos of the current page that the user
     * can access.
     *
     * @param User   $currentUser
     * @param string $controller
     * @param string $action
     * @param string $slug_or_id
     *
     * @return array
     */
    public function get(User $currentUser, $controller, $action, $slug_or_id)
    {
        $photos = array();
        $currentlyRendered = null;

        $repo = $this->em->getRepository(
            'Khatovar' . ucfirst($controller) . 'Bundle:' . ucfirst($controller)
        );

        if ($controller == 'homepage'
            and is_null($slug_or_id)
            and $action != 'create'
            and $action != 'list') {
            $currentlyRendered = $repo->findOneBy(array('active' => true));
        } elseif (!is_null($slug_or_id)) {
            if (is_string($slug_or_id)) {
                $currentlyRendered = $repo->findOneBy(array('slug' => $slug_or_id));
            } elseif (is_int($slug_or_id)) {
                $currentlyRendered = $repo->find($slug_or_id);
            }
        }

        $owner = null;
        if ($controller == 'member' and !is_null($slug_or_id)) {
            $owner = $currentlyRendered->getOwner();
        }

        if (!is_null($currentlyRendered)) {
            if ($currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')
                or ($owner == $currentUser)) {
                $photos = $currentlyRendered->getPhotos();
            }
        }

        return $photos;
    }
}
