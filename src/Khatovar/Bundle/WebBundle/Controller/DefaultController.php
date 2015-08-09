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

namespace Khatovar\Bundle\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\WebBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @param int $atelier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function campAction($atelier)
    {
        return $this->render(
            'KhatovarWebBundle:Default:camp-' . $atelier . '.html.twig'
        );
    }

    /**
     * @param string $pratique
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fightAction($pratique)
    {
        return $this->render(
            'KhatovarWebBundle:Default:combat-' . $pratique . '.html.twig'
        );
    }
}
