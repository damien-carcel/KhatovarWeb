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

namespace Khatovar\Bundle\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ContactController extends Controller
{
    /**
     * Displays the active contact page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $activeContact = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Contact')
            ->findActiveOr404();

        return $this->render(
            'KhatovarWebBundle:Contact:show.html.twig',
            ['contact' => $activeContact]
        );
    }

    /**
     * Finds and displays a contact page.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $contact = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Contact')
            ->findByIdOr404($id);

        return $this->render(
            'KhatovarWebBundle:Contact:show.html.twig',
            ['contact' => $contact]
        );
    }
}
