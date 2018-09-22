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

namespace Khatovar\Bundle\WebBundle\Controller\Admin;

use Khatovar\Bundle\WebBundle\Entity\Exaction;
use Khatovar\Bundle\WebBundle\Form\Type\ExactionType;
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
            'KhatovarWebBundle:Exaction:new.html.twig',
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

            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->persist($exaction);
            $entityManager->flush();

            $this->addFlash('notice', 'Exaction créée');

            return $this->redirect($this->chooseRedirectionUrl($exaction));
        }

        return $this->render(
            'KhatovarWebBundle:Exaction:new.html.twig',
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
        $exaction = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Exaction')
            ->findByIdOr404($id);

        $exactionPassed = true;
        if ($exaction->getStart() >= new \DateTime()) {
            $exactionPassed = false;
        }

        $editForm = $this->createEditForm($exaction);

        return $this->render(
            'KhatovarWebBundle:Exaction:edit.html.twig',
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
        $exaction = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Exaction')
            ->findByIdOr404($id);

        $exactionPassed = true;
        if ($exaction->getStart() >= new \DateTime()) {
            $exactionPassed = false;
        }

        $editForm = $this->createEditForm($exaction);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->get('doctrine')->getManager()->flush();

            $this->addFlash('notice', 'Exaction modifiée');

            return $this->redirect($this->chooseRedirectionUrl($exaction));
        }

        return $this->render(
            'KhatovarWebBundle:Exaction:edit.html.twig',
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
        $exaction = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Exaction')
            ->findByIdOr404($id);

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->remove($exaction);
            $entityManager->flush();

            $this->addFlash('notice', 'Exaction supprimée');
        }

        return $this->redirect($this->chooseRedirectionUrl($exaction));
    }

    /**
     * Creates a form to create a Exaction entity.
     *
     * @param Exaction $exaction
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createCreateForm(Exaction $exaction)
    {
        $form = $this->createForm(
            ExactionType::class,
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
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(Exaction $exaction)
    {
        $form = $this->createForm(
            ExactionType::class,
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
     * Generate the correct URL for redirection according to exaction
     * date (past or to come).
     *
     * @param Exaction $exaction
     *
     * @return string
     */
    protected function chooseRedirectionUrl(Exaction $exaction)
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
