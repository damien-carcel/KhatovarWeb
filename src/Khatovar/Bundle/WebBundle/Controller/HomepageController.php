<?php

declare(strict_types=1);

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
class HomepageController extends Controller
{
    /**
     * Displays the active homepage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $homepage = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Homepage')
            ->findActiveOr404();

        return $this->render(
            'KhatovarWebBundle:Homepage:show.html.twig',
            [
                'content' => $this->get('khatovar_photo.manager.photo')->imageTranslate($homepage->getContent()),
                'page_id' => $homepage->getId(),
            ]
        );
    }

    /**
     * Finds and display a homepage.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $homepage = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Homepage')
            ->findByIdOr404($id);

        return $this->render(
            'KhatovarWebBundle:Homepage:show.html.twig',
            [
                'content' => $this->get('khatovar_photo.manager.photo')->imageTranslate($homepage->getContent()),
                'page_id' => $homepage->getId(),
            ]
        );
    }
}
