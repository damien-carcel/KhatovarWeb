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

use Doctrine\ORM\EntityManagerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\ExactionBundle\Entity\Exaction;
use Khatovar\Bundle\ExactionBundle\Manager\ExactionManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Main Controller for Exaction bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ExactionController extends Controller
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ExactionManager */
    protected $exactionManager;

    /** @var Session */
    protected $session;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Session                $session
     * @param ExactionManager        $exactionManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Session $session,
        ExactionManager $exactionManager
    ) {
        $this->entityManager   = $entityManager;
        $this->session         = $session;
        $this->exactionManager = $exactionManager;
    }

    /**
     * Displays a generalist exaction page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $activeYears = $this->exactionManager->getSortedYears();

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
        $activeYears = $this->exactionManager->getSortedYears();

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
        $futureExactions = $this->entityManager
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->getFutureExactions();

        $deleteForms = $this->createDeleteForms($futureExactions);

        return $this->render(
            'KhatovarExactionBundle:Exaction:to_come.html.twig',
            [
                'future_exactions' => $futureExactions,
                'delete_forms'     => $deleteForms,
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
        $exactions = $this->entityManager
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->getExactionsByYear($year);

        $deleteForms = $this->createDeleteForms($exactions);

        return $this->render(
            'KhatovarExactionBundle:Exaction:view_by_year.html.twig',
            [
                'exactions'    => $exactions,
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * Displays a form to create a new exaction.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function newAction()
    {
        $exaction = new Exaction();

        $form = $this->createCreateForm($exaction);

        return $this->render(
            'KhatovarExactionBundle:Exaction:new.html.twig',
            [
                'form'            => $form->createView(),
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
     * @Secure(roles="ROLE_EDITOR")
     */
    public function createAction(Request $request)
    {
        $exaction = new Exaction();

        $form = $this->createCreateForm($exaction);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $exaction->setOnlyPhotos(true);

            $this->entityManager->persist($exaction);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Exaction créée'
            );

            return $this->redirect($this->chooseRedirectionURL($exaction));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:new.html.twig',
            [
                'form'            => $form->createView(),
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
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction($id)
    {
        $exaction = $this->findByIdOr404($id);

        $exactionPassed = true;
        if ($exaction->getStart() >= new \DateTime()) {
            $exactionPassed = false;
        }

        $editForm = $this->createEditForm($exaction);

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            [
                'edit_form'       => $editForm->createView(),
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
     * @Secure(roles="ROLE_EDITOR")
     */
    public function updateAction(Request $request, $id)
    {
        $exaction = $this->findByIdOr404($id);

        $exactionPassed = true;
        if ($exaction->getStart() >= new \DateTime()) {
            $exactionPassed = false;
        }

        $editForm = $this->createEditForm($exaction);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Exaction modifiée'
            );

            return $this->redirect($this->chooseRedirectionURL($exaction));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            [
                'edit_form'       => $editForm->createView(),
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
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Request $request, $id)
    {
        $exaction = $this->findByIdOr404($id);

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->remove($exaction);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Exaction supprimée'
            );
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
            'khatovar_exaction_type',
            $exaction,
            [
                'action' => $this->generateUrl('khatovar_web_exaction_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Créer']);

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
            'khatovar_exaction_type',
            $exaction,
            [
                'action' => $this->generateUrl('khatovar_web_exaction_update', ['id' => $exaction->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Mettre à jour']);

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
                'submit',
                [
                    'label' => 'Effacer',
                    'attr'  => ['onclick' => 'return confirm("Êtes-vous sûr ?")'],
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
     * @param int $id
     *
     * @return Exaction
     */
    protected function findByIdOr404($id)
    {
        $exaction = $this->entityManager->getRepository('KhatovarExactionBundle:Exaction')->find($id);

        if (!$exaction) {
            throw $this->createNotFoundException('Impossible de trouver l\'exaction.');
        }

        return $exaction;
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
        $isExactionPassed = $this->exactionManager->isExactionPassed($exaction);

        if ($isExactionPassed) {
            return $this->generateUrl(
                'khatovar_web_exaction_list_by_year',
                ['year' => $exaction->getStart()->format('Y')]
            );
        }

        return $this->generateUrl('khatovar_web_exaction_to_come');
    }
}
