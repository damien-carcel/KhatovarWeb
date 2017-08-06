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

namespace Khatovar\Bundle\ExactionBundle\Controller;

use Khatovar\Bundle\ExactionBundle\Entity\Exaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

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
            'KhatovarExactionBundle:Exaction:index.html.twig',
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
            'KhatovarExactionBundle:Exaction:past.html.twig',
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
        $futureExactions = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->getFutureExactions();

        $deleteForms = $this->createDeleteForms($futureExactions);

        return $this->render(
            'KhatovarExactionBundle:Exaction:to_come.html.twig',
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
        $exactions = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->getExactionsByYear($year);

        $deleteForms = $this->createDeleteForms($exactions);

        return $this->render(
            'KhatovarExactionBundle:Exaction:view_by_year.html.twig',
            [
                'exactions' => $exactions,
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * Displays a form to create a new exaction.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction()
    {
        $exaction = new Exaction();
        $form = $this->createCreateForm($exaction);

        return $this->render(
            'KhatovarExactionBundle:Exaction:new.html.twig',
            [
                'form' => $form->createView(),
                'exaction_passed' => false,
            ]
        );
    }

    /**
     * Creates a new exaction.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function createAction(Request $request)
    {
        $exaction = new Exaction();

        $form = $this->createCreateForm($exaction);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $exaction->setOnlyPhotos(true);

            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->persist($exaction);
            $entityManager->flush();

            $this->addFlash('notice', 'Exaction créée');

            return $this->redirect($this->chooseRedirectionURL($exaction));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:new.html.twig',
            [
                'form' => $form->createView(),
                'exaction_passed' => false,
            ]
        );
    }

    /**
     * Displays a form to edit an existing exaction.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function editAction($id)
    {
        $exaction = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->findByIdOr404($id);

        $exactionPassed = true;
        if ($exaction->getStart() >= new \DateTime()) {
            $exactionPassed = false;
        }

        $editForm = $this->createEditForm($exaction);

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            [
                'edit_form' => $editForm->createView(),
                'exaction_passed' => $exactionPassed,
            ]
        );
    }

    /**
     * Updates an exaction.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function updateAction(Request $request, $id)
    {
        $exaction = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->findByIdOr404($id);

        $exactionPassed = true;
        if ($exaction->getStart() >= new \DateTime()) {
            $exactionPassed = false;
        }

        $editForm = $this->createEditForm($exaction);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->get('doctrine.orm.entity_manager')->flush();

            $this->addFlash('notice', 'Exaction modifiée');

            return $this->redirect($this->chooseRedirectionURL($exaction));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            [
                'edit_form' => $editForm->createView(),
                'exaction_passed' => $exactionPassed,
            ]
        );
    }

    /**
     * Deletes an exaction.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function deleteAction(Request $request, $id)
    {
        $exaction = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->findByIdOr404($id);

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->remove($exaction);
            $entityManager->flush();

            $this->addFlash('notice', 'Exaction supprimée');
        }

        return $this->redirect($this->chooseRedirectionURL($exaction));
    }

    /**
     * Creates a form to create a Exaction entity.
     *
     * @param Exaction $exaction
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(Exaction $exaction)
    {
        $form = $this->createForm(
            'Khatovar\Bundle\ExactionBundle\Form\Type\ExactionType',
            $exaction,
            [
                'action' => $this->generateUrl('khatovar_web_exaction_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Créer']);

        return $form;
    }

    /**
     * Creates a form to edit a Exaction entity.
     *
     * @param Exaction $exaction
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createEditForm(Exaction $exaction)
    {
        $form = $this->createForm(
            'Khatovar\Bundle\ExactionBundle\Form\Type\ExactionType',
            $exaction,
            [
                'action' => $this->generateUrl('khatovar_web_exaction_update', ['id' => $exaction->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Mettre à jour']);

        return $form;
    }

    /**
     * Creates a form to delete a Exaction entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\Form The form
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
     * @return \Symfony\Component\Form\Form[]
     */
    protected function createDeleteForms(array $exactions)
    {
        $deleteForms = [];

        foreach ($exactions as $exaction) {
            $deleteForms[$exaction->getId()] = $this->createDeleteForm($exaction->getId())->createView();
        }

        return $deleteForms;
    }

    /**
     * Generate the correct URL for redirection according to exaction
     * date (past or to come).
     *
     * @param Exaction $exaction
     *
     * @return string
     */
    protected function chooseRedirectionURL(Exaction $exaction)
    {
        $isExactionPassed = $this->get('khatovar_exaction.manager.exaction')->isExactionPassed($exaction);

        if ($isExactionPassed) {
            return $this->generateUrl(
                'khatovar_web_exaction_list_by_year',
                ['year' => $exaction->getStart()->format('Y')]
            );
        }

        return $this->generateUrl('khatovar_web_exaction_to_come');
    }
}
