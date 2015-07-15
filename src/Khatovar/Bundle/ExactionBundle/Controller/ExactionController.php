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
use Khatovar\Bundle\ExactionBundle\Form\ExactionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Exaction controller.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
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
        $yearLister = $this->get('khatovar.exaction.lister.year');
        $activeYears = $yearLister->getSortedYears();

        return $this->render(
            'KhatovarExactionBundle:Exaction:index.html.twig',
            array('active_years' => $activeYears)
        );
    }

    /**
     * View the schedule of the current year.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toComeAction()
    {
        $entityManager = $this->getDoctrine()->getRepository('KhatovarExactionBundle:Exaction');

        $futureExactions = $entityManager->getFutureExactions();

        return $this->render(
            'KhatovarExactionBundle:Exaction:to_come.html.twig',
            array('future_exactions' => $futureExactions)
        );
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
        $entityManager = $this->getDoctrine()->getRepository('KhatovarExactionBundle:Exaction');

        $exactions = $entityManager->getExactionsByYear($year);

        return $this->render(
            'KhatovarExactionBundle:Exaction:view_by_year.html.twig',
            array('exactions' => $exactions)
        );
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
        $exactionExists = false;
        $exaction = new Exaction();
        $form = $this->createForm(new ExactionType($exactionExists), $exaction);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exaction);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'La nouvelle exaction a bien été sauvegardée.'
            );

            return $this->redirect($this->generateUrl('khatovar_web_exaction_to_come'));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            array(
                'form'            => $form->createView(),
                'exaction_exists' => $exactionExists,
            )
        );
    }

    /**
     * Edit an exaction.
     *
     * @param Exaction $exaction
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction(Exaction $exaction, Request $request)
    {
        $exactionExists = true;
        $form = $this->createForm(new ExactionType($exactionExists), $exaction);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exaction);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'L\'exaction a bien été mise à jour.'
            );

            return $this->redirect($this->generateUrl('khatovar_web_exaction_to_come'));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            array(
                'form'            => $form->createView(),
                'exaction_exists' => $exactionExists,
            )
        );
    }

    /**
     * Remove an exaction.
     *
     * @param Exaction $exaction
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function removeAction(Exaction $exaction, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($exaction);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Exaction supprimée'
            );

            return $this->redirect(
                $this->generateUrl('khatovar_web_exaction_to_come')
            );
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:remove.html.twig',
            array(
                'exaction' => $exaction,
                'form'     => $form->createView(),
            )
        );
    }
}
