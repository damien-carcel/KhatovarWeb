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

namespace Khatovar\Bundle\WebBundle\Controller\Admin;

use Khatovar\Bundle\WebBundle\Entity\Appearance;
use Khatovar\Bundle\WebBundle\Form\Type\AppearanceType;
use Khatovar\Bundle\WebBundle\Helper\AppearanceHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Appearance bundle main controller. Only perform display actions.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AppearanceController extends Controller
{
    /**
     * Lists all appearances.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function listAction()
    {
        $appearances = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Appearance')
            ->findAll();

        $deleteForms = $this->createDeleteForms($appearances);

        return $this->render(
            'KhatovarWebBundle:Appearance:list.html.twig',
            [
                'appearances' => $appearances,
                'delete_forms' => $deleteForms,
                'helper' => AppearanceHelper::getAppearancePageTypes(),
            ]
        );
    }

    /**
     * Displays a form to create a new appearance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction()
    {
        $appearance = new Appearance();

        $form = $this->createCreateForm($appearance);

        return $this->render(
            'KhatovarWebBundle:Appearance:new.html.twig',
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
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function createAction(Request $request)
    {
        $appearance = new Appearance();

        $form = $this->createCreateForm($appearance);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->persist($appearance);
            $entityManager->flush();

            $this->addFlash('notice', 'Prestation créée');

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_appearance_show',
                    ['slug' => $appearance->getSlug()]
                )
            );
        }

        return $this->render(
            'KhatovarWebBundle:Appearance:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit the active introduction page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function editIndexAction()
    {
        $introduction = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Appearance')
            ->findActiveIntroduction();

        return $this->editAction($introduction->getId());
    }

    /**
     * Displays a form to edit an existing Appearance entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function editAction($id)
    {
        $appearance = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Appearance')
            ->findByIdOr404($id);

        $workshops = $this->findActiveWorkshopsIfIsProgramme($appearance);
        $editForm = $this->createEditForm($appearance);

        return $this->render(
            'KhatovarWebBundle:Appearance:edit.html.twig',
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
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function updateAction(Request $request, $id)
    {
        $appearance = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Appearance')
            ->findByIdOr404($id);

        $workshops = $this->findActiveWorkshopsIfIsProgramme($appearance);
        $editForm = $this->createEditForm($appearance);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->get('doctrine')->getManager()->flush();

            $this->addFlash('notice', 'Prestation modifiée');

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_appearance_show',
                    ['slug' => $appearance->getSlug()]
                )
            );
        }

        return $this->render(
            'KhatovarWebBundle:Appearance:edit.html.twig',
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
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $appearance = $this->get('doctrine')
                ->getRepository('KhatovarWebBundle:Appearance')
                ->findByIdOr404($id);

            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->remove($appearance);
            $entityManager->flush();

            $this->addFlash('notice', 'Prestation supprimée');
        }

        return $this->redirect($this->generateUrl('khatovar_web_appearance_list'));
    }

    /**
     * Creates a form to create a Appearance entity.
     *
     * @param Appearance $entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createCreateForm(Appearance $entity)
    {
        $form = $this->createForm(
            AppearanceType::class,
            $entity,
            [
                'action' => $this->generateUrl('khatovar_web_appearance_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Créer']);

        return $form;
    }

    /**
     * Creates a form to edit a Appearance entity.
     *
     * @param Appearance $entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(Appearance $entity)
    {
        $form = $this->createForm(
            AppearanceType::class,
            $entity,
            [
                'action' => $this->generateUrl('khatovar_web_appearance_update', ['id' => $entity->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Mettre à jour']);

        return $form;
    }

    /**
     * Creates a form to delete a Appearance entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_appearance_delete', ['id' => $id]))
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
     * Get active workshops if given appearance is a programme.
     *
     * @param Appearance $appearance
     *
     * @return \Khatovar\Bundle\WebBundle\Entity\Appearance[]
     */
    protected function findActiveWorkshopsIfIsProgramme(Appearance $appearance)
    {
        $workshops = [];

        if (AppearanceHelper::PROGRAMME_TYPE_CODE === $appearance->getPageType()) {
            $workshops = $this->get('doctrine')
                ->getRepository('KhatovarWebBundle:Appearance')
                ->findActiveWorkshopsSortedBySlug();
        }

        return $workshops;
    }
}
