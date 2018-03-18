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
 * Appearance bundle main controller. Only perform display actions.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AppearanceController extends Controller
{
    /**
     * Lists all programmes.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $appearancesRepo = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Appearance');

        $appearances = $appearancesRepo->findActiveProgrammesSortedBySlug();
        $introduction = $appearancesRepo->findActiveIntroduction();

        return $this->render(
            'KhatovarWebBundle:Appearance:index.html.twig',
            [
                'appearances' => $appearances,
                'introduction' => $introduction,
            ]
        );
    }

    /**
     * Lists all workshops.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function workshopAction()
    {
        $appearances = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Appearance')
            ->findActiveWorkshopsSortedBySlug();

        return $this->render(
            'KhatovarWebBundle:Appearance:index.html.twig',
            [
                'appearances' => $appearances,
                'introduction' => null,
            ]
        );
    }

    /**
     * Displays the camp page.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function campAction()
    {
        $camp = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Appearance')
            ->findActiveCamp();

        return $this->render(
            'KhatovarWebBundle:Appearance:show.html.twig',
            [
                'previous' => null,
                'appearance' => $camp,
                'next' => null,
            ]
        );
    }

    /**
     * Finds and displays an appearance.
     *
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slug)
    {
        $appearances = $this->get('khatovar_appearance.manager.appearance')->findWithNextAndPreviousOr404($slug);

        return $this->render(
            'KhatovarWebBundle:Appearance:show.html.twig',
            [
                'previous' => $appearances['previous'],
                'appearance' => $appearances['current'],
                'next' => $appearances['next'],
            ]
        );
    }
}
