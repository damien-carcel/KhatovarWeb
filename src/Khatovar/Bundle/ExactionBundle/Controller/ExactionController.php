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
use Khatovar\Bundle\ExactionBundle\Form\ExactionType;
use Khatovar\Bundle\ExactionBundle\Manager\ExactionManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Main Controller for Exaction bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ExactionController extends Controller
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ExactionManager */
    protected $exactionManager;

    /**
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     * @param ExactionManager        $exactionManager
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        ExactionManager $exactionManager
    ) {
        $this->container       = $container;
        $this->entityManager   = $entityManager;
        $this->exactionManager = $exactionManager;
    }

    /**
     * Display the list of all years of exaction.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $activeYears = $this->exactionManager->getSortedYears();

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
        $futureExactions = $this->entityManager
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->getFutureExactions();

        $deleteForms = $this->createDeleteForms($futureExactions);

        return $this->render(
            'KhatovarExactionBundle:Exaction:to_come.html.twig',
            array(
                'future_exactions' => $futureExactions,
                'delete_forms'     => $deleteForms,
            )
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
        $exactions = $this->entityManager
            ->getRepository('KhatovarExactionBundle:Exaction')
            ->getExactionsByYear($year);

        $deleteForms = $this->createDeleteForms($exactions);

        return $this->render(
            'KhatovarExactionBundle:Exaction:view_by_year.html.twig',
            array(
                'exactions'    => $exactions,
                'delete_forms' => $deleteForms,
            )
        );
    }

    /**
     * Displays a form to create a new Contact entity.
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
            array(
                'form'            => $form->createView(),
                'exaction_exists' => false,
                )
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
    public function createAction(Request $request)
    {
        $exaction = new Exaction();

        $form = $this->createCreateForm($exaction);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->persist($exaction);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'La nouvelle exaction a bien été sauvegardée.'
            );

            return $this->redirect($this->chooseRedirectionURL($exaction));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:new.html.twig',
            array(
                'form'            => $form->createView(),
                'exaction_exists' => false,
            )
        );
    }

    /**
     * Displays a form to edit an existing Exaction entity.
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

        $editForm = $this->createEditForm($exaction);

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            array(
                'edit_form'       => $editForm->createView(),
                'exaction_exists' => true,
            )
        );
    }

    /**
     * Update an exaction.
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

        $editForm = $this->createEditForm($exaction);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'L\'exaction a bien été mise à jour.'
            );

            return $this->redirect($this->chooseRedirectionURL($exaction));
        }

        return $this->render(
            'KhatovarExactionBundle:Exaction:edit.html.twig',
            array(
                'edit_form'       => $editForm->createView(),
                'exaction_exists' => true,
            )
        );
    }

    /**
     * Remove an exaction.
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

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Page de contact supprimée'
            );
        }

        return $this->redirect($this->chooseRedirectionURL($exaction));
    }

    /**
     * Creates a form to create a Contact entity.
     *
     * @param Exaction $exaction
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(Exaction $exaction)
    {
        $exactionExists = false;

        $form = $this->createForm(
            new ExactionType($exactionExists),
            $exaction,
            array(
                'action' => $this->generateUrl('khatovar_web_exaction_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Créer'));

        return $form;
    }

    /**
     * Creates a form to edit a Contact entity.
     *
     * @param Exaction $exaction
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createEditForm(Exaction $exaction)
    {
        $exactionExists = true;

        $form = $this->createForm(
            new ExactionType($exactionExists),
            $exaction,
            array(
                'action' => $this->generateUrl('khatovar_web_exaction_update', array('id' => $exaction->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Mettre à jour'));

        return $form;
    }

    /**
     * Creates a form to delete a Contact entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_exaction_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit',
                array(
                    'label' => 'Effacer',
                    'attr'  => array('onclick' => 'return confirm("Êtes-vous sûr ?")'),
                )
            )
            ->getForm();
    }

    /**
     * Return a list of delete forms for a set of contacts.
     *
     * @param Exaction[] $exactions
     *
     * @return \Symfony\Component\Form\Form[]
     */
    protected function createDeleteForms(array $exactions)
    {
        $deleteForms = array();

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
                array('year' => $exaction->getStart()->format('Y'))
            );
        }

        return $this->generateUrl('khatovar_web_exaction_to_come');
    }
}
