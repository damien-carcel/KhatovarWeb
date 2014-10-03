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

namespace Khatovar\WebBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\WebBundle\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Photo controller.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Controller
 */
class PhotoController extends Controller
{
    /**
     * Return the list of all photos uploaded for the website and
     * display admin utilities to manage them.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('KhatovarWebBundle:Photo:index.html.twig');
    }

    /**
     * Display a list of all photos uploaded for the current page in a
     * small sidebar.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function sideAction()
    {
        return $this->render('KhatovarWebBundle:Photo:side.html.twig');
    }

    /**
     * Add a new photo to the collection.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function addAction()
{
        return $this->render('KhatovarWebBundle:Photo:add.html.twig');
    }

    /**
     * Edit a photo informations.
     *
     * @param Photo $photo
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction(Photo $photo)
    {
        return $this->render('KhatovarWebBundle:Photo:edit.html.twig');
    }

    /**
     * Remove a photo from the collection.
     *
     * @param Photo $photo
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Photo $photo)
    {
        return $this->render('KhatovarWebBundle:Photo:delete.html.twig');
    }
}
