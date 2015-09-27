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

namespace Khatovar\Bundle\AppearanceBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\AppearanceBundle\Entity\Appearance;
use Khatovar\Bundle\AppearanceBundle\Helper\AppearanceHelper;
use Khatovar\Bundle\AppearanceBundle\Manager\AppearanceManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Appearance bundle main controller.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AppearanceController extends Controller
{
    /** @var AppearanceManager */
    protected $appearanceManager;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Session */
    protected $session;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Session                $session
     * @param AppearanceManager      $appearanceManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Session $session,
        AppearanceManager $appearanceManager
    ) {
        $this->entityManager     = $entityManager;
        $this->session           = $session;
        $this->appearanceManager = $appearanceManager;
    }

    /**
     * Lists all programmes.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $appearances = $this->entityManager
            ->getRepository('KhatovarAppearanceBundle:Appearance')
            ->findActiveProgrammesSortedBySlug();

        $introduction = $this->entityManager
            ->getRepository('KhatovarAppearanceBundle:Appearance')
            ->findActiveIntroduction();

        if (null === $introduction) {
            throw $this->createNotFoundException('Impossible de trouver une page d\'introduction active.');
        }

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:index.html.twig',
            [
                'appearances'  => $appearances,
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
        $appearances = $this->entityManager
            ->getRepository('KhatovarAppearanceBundle:Appearance')
            ->findActiveWorkshopsSortedBySlug();

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:index.html.twig',
            ['appearances' => $appearances]
        );
    }

    /**
     * Displays the camp page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function campAction()
    {
        $camp = $this->entityManager->getRepository('KhatovarAppearanceBundle:Appearance')->findActiveCamp();

        if (null === $camp) {
            throw $this->createNotFoundException('Impossible de trouver une page de camp active.');
        }

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:show.html.twig',
            [
                'previous'   => null,
                'appearance' => $camp,
                'next'       => null,
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
        $appearances = $this->appearanceManager->findWithNextAndPreviousOr404($slug);

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:show.html.twig',
            [
                'previous'   => $appearances['previous'],
                'appearance' => $appearances['current'],
                'next'       => $appearances['next'],
            ]
        );
    }

    /**
     * Lists all appearances.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function listAction()
    {
        $appearances = $this->entityManager->getRepository('KhatovarAppearanceBundle:Appearance')->findAll();
        $deleteForms = $this->createDeleteForms($appearances);

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:list.html.twig',
            [
                'appearances'  => $appearances,
                'delete_forms' => $deleteForms,
                'helper'       => AppearanceHelper::getAppearancePageTypes(),
            ]
        );
    }

    /**
     * Displays a form to create a new appearance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function newAction()
    {
        $appearance = new Appearance();

        $form = $this->createCreateForm($appearance);

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Creates a new appearance.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function createAction(Request $request)
    {
        $appearance = new Appearance();

        $form = $this->createCreateForm($appearance);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->persist($appearance);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Prestation créée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_appearance_show',
                    ['slug' => $appearance->getSlug()]
                )
            );
        }

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function editIndexAction()
    {
        $introduction = $this->entityManager
            ->getRepository('KhatovarAppearanceBundle:Appearance')
            ->findActiveIntroduction();

        if (null === $introduction) {
            throw $this->createNotFoundException('Impossible de trouver une page d\'introduction active.');
        }

        return $this->editAction($introduction->getId());
    }

    /**
     * Displays a form to edit an existing Appearance entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction($id)
    {
        $appearance = $this->findByIdOr404($id);
        $workshops  = $this->findActiveWorkshopsIfIsProgramme($appearance);

        $editForm = $this->createEditForm($appearance);

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:edit.html.twig',
            [
                'edit_form' => $editForm->createView(),
                'workshops' => $workshops,
            ]
        );
    }

    /**
     * Edits an existing Appearance entity.
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
        $appearance = $this->findByIdOr404($id);
        $workshops  = $this->findActiveWorkshopsIfIsProgramme($appearance);

        $editForm = $this->createEditForm($appearance);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Prestation modifiée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_appearance_show',
                    ['slug' => $appearance->getSlug()]
                )
            );
        }

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:edit.html.twig',
            [
                'edit_form' => $editForm->createView(),
                'workshops' => $workshops,
            ]
        );
    }

    /**
     * Deletes a Appearance entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $appearance = $this->findByIdOr404($id);

            $this->entityManager->remove($appearance);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Prestation supprimée'
            );
        }

        return $this->redirect($this->generateUrl('khatovar_web_appearance_list'));
    }

    /**
     * Creates a form to create a Appearance entity.
     *
     * @param Appearance $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(Appearance $entity)
    {
        $form = $this->createForm(
            'khatovar_appearance_type',
            $entity,
            [
                'action' => $this->generateUrl('khatovar_web_appearance_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Créer']);

        return $form;
    }

    /**
     * Creates a form to edit a Appearance entity.
     *
     * @param Appearance $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(Appearance $entity)
    {
        $form = $this->createForm(
            'khatovar_appearance_type',
            $entity,
            [
                'action' => $this->generateUrl('khatovar_web_appearance_update', ['id' => $entity->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Mettre à jour']);

        return $form;
    }

    /**
     * Creates a form to delete a Appearance entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_appearance_delete', ['id' => $id]))
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
     * @param Appearance[] $appearances
     *
     * @return \Symfony\Component\Form\Form[]
     */
    protected function createDeleteForms(array $appearances)
    {
        $deleteForms = [];

        foreach ($appearances as $appearance) {
            $deleteForms[$appearance->getId()] = $this->createDeleteForm($appearance->getId())->createView();
        }

        return $deleteForms;
    }

    /**
     * @param int $id
     *
     * @return Appearance
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findByIdOr404($id)
    {
        $appearance = $this->entityManager->getRepository('KhatovarAppearanceBundle:Appearance')->find($id);

        if (!$appearance) {
            throw $this->createNotFoundException('Impossible de trouver la prestation.');
        }

        return $appearance;
    }

    /**
     * Get active workshops if given appearance is a programme.
     *
     * @param Appearance $appearance
     *
     * @return \Khatovar\Bundle\AppearanceBundle\Entity\Appearance[]
     */
    protected function findActiveWorkshopsIfIsProgramme(Appearance $appearance)
    {
        $workshops = [];

        if (AppearanceHelper::PROGRAMME_TYPE_CODE === $appearance->getPageType()) {
            $workshops = $this->entityManager
                ->getRepository('KhatovarAppearanceBundle:Appearance')
                ->findActiveWorkshopsSortedBySlug();
        }

        return $workshops;
    }
}
