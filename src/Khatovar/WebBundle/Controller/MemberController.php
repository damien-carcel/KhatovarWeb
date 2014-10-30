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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MemberController
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Controller
 */
class MemberController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('KhatovarWebBundle:Member:index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction()
    {
        return $this->render('KhatovarWebBundle:Member:view.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function addAction()
    {
        // TODO: make sure one cannot create a new member with the same name than an other one

        return $this->render('KhatovarWebBundle:Member:add.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction()
    {
        return $this->render('KhatovarWebBundle:Member:edit.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function removeAction()
    {
        return $this->render('KhatovarWebBundle:Member:remove.html.twig');
    }
}
