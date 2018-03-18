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

use Khatovar\Bundle\WebBundle\Entity\Exaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ExactionController extends Controller
{
    /**
     * Displays a generalist exaction page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $activeYears = $this->get('khatovar_exaction.manager.exaction')->getSortedYears();

        return $this->render(
            'KhatovarWebBundle:Exaction:index.html.twig',
            ['active_years' => $activeYears]
        );
    }

    /**
     * Displays the list of all years of exaction.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pastAction()
    {
        $activeYears = $this->get('khatovar_exaction.manager.exaction')->getSortedYears();

        return $this->render(
            'KhatovarWebBundle:Exaction:past.html.twig',
            ['active_years' => $activeYears]
        );
    }

    /**
     * Displays the schedule of the current year.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toComeAction()
    {
        $futureExactions = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Exaction')
            ->getFutureExactions();

        $deleteForms = $this->createDeleteForms($futureExactions);

        return $this->render(
            'KhatovarWebBundle:Exaction:to_come.html.twig',
            [
                'future_exactions' => $futureExactions,
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * Displays the exactions of a given year.
     *
     * @param int $year
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewByYearAction($year)
    {
        $exactions = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Exaction')
            ->getExactionsByYear($year);

        $deleteForms = $this->createDeleteForms($exactions);

        return $this->render(
            'KhatovarWebBundle:Exaction:view_by_year.html.twig',
            [
                'exactions' => $exactions,
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * Creates a form to delete a Exaction entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_exaction_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Effacer',
                    'attr' => ['onclick' => 'return confirm("Êtes-vous sûr ?")'],
                ]
            )
            ->getForm();
    }

    /**
     * Return a list of delete forms for a set of Exaction entities.
     *
     * @param Exaction[] $exactions
     *
     * @return \Symfony\Component\Form\FormInterface[]
     */
    protected function createDeleteForms(array $exactions)
    {
        $deleteForms = [];

        foreach ($exactions as $exaction) {
            $deleteForms[$exaction->getId()] = $this->createDeleteForm($exaction->getId())->createView();
        }

        return $deleteForms;
    }
}
