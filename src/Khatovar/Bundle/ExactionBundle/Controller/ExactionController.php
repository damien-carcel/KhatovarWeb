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
 * @copyright Copyright (C) Damien Carcel (https://github.com/damien-carcel)
 * @link      https://github.com/damien-carcel/KhatovarWeb
 * @license   http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\ExactionBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\ExactionBundle\Entity\Exaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ScheduleController
 *
 * @author  Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\ScheduleBundle\Controller
 */
class ExactionController extends Controller
{
    /**
     * Display the list of all years of exaction.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('KhatovarExactionBundle:Exaction:index.html.twig');
    }

    /**
     * View the schedule of the current year.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toComeAction()
    {
        return $this->render('KhatovarExactionBundle:Exaction:to_come.html.twig');
    }

    /**
     * View the exactions of a given year.
     *
     * @param int $year
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewByYearAction($year)
    {
        return $this->render('KhatovarExactionBundle:Exaction:view_by_year.html.twig');
    }

    /**
     * Add a new exaction.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function addAction(Request $request)
    {
        return $this->render('KhatovarExactionBundle:Exaction:edit.html.twig');
    }

    /**
     * Edit an exaction.
     *
     * @param Exaction $exaction
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction(Exaction $exaction, Request $request)
    {
        return $this->render('KhatovarExactionBundle:Exaction:edit.html.twig');
    }

    /**
     * Remove an exaction.
     *
     * @param Exaction $exaction
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function removeAction(Exaction $exaction, Request $request)
    {
        return $this->render('KhatovarExactionBundle:Exaction:remove.html.twig');
    }
}
