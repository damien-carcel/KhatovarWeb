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
use Khatovar\Bundle\AppearanceBundle\Entity\Appearance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Appearance bundle main controller.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AppearanceController extends Controller
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Session */
    protected $session;

    /**
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     * @param Session                $session
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        Session $session
    ) {
        $this->container     = $container;
        $this->entityManager = $entityManager;
        $this->session       = $session;
    }

    /**
     * Lists all appearances.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $appearances = $this->entityManager->getRepository('KhatovarAppearanceBundle:Appearance')->findAll();

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:index.html.twig',
            array('appearances' => $appearances)
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
        $appearance = $this->findBySlugOr404($slug);

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:show.html.twig',
            array('appearance' => $appearance)
        );
    }

    /**
     * Displays a form to create a new appearance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $entity = new Appearance();

        $form = $this->createCreateForm($entity);

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:new.html.twig',
            array('form' => $form->createView(),)
        );
    }

    /**
     * Creates a new appearance.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $entity = new Appearance();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Prestation créée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_appearance_show',
                    array('id' => $entity->getId())
                )
            );
        }

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Displays a form to edit an existing Appearance entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $appearance = $this->findByIdOr404($id);

        $editForm = $this->createEditForm($appearance);

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:edit.html.twig',
            array('edit_form' => $editForm->createView(),)
        );
    }

    /**
     * Edits an existing Appearance entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        $appearance = $this->findByIdOr404($id);

        $editForm = $this->createEditForm($appearance);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Prestation modifiée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_appearance_edit',
                    array('id' => $id)
                )
            );
        }

        return $this->render(
            'KhatovarAppearanceBundle:Appearance:edit.html.twig',
            array('edit_form' => $editForm->createView(),)
        );
    }

    /**
     * Deletes a Appearance entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $appearance = $this->findByIdOr404($id);

            $this->entityManager->remove($appearance);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Prestation supprimée'
            );
        }

        return $this->redirect($this->generateUrl('khatovar_web_appearance'));
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
            array(
                'action' => $this->generateUrl('khatovar_web_appearance_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Créer'));

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
            array(
                'action' => $this->generateUrl('khatovar_web_appearance_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Mettre à jour'));

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
            ->setAction($this->generateUrl('khatovar_web_appearance_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit',
                array(
                    'label' => 'Delete',
                    'attr'  => array('onclick' => 'return confirm("Êtes-vous sûr ?")'),
                )
            )
            ->getForm()
        ;
    }

    /**
     * @param int $id
     *
     * @return Appearance
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
     * @param string $slug
     *
     * @return Appearance
     */
    protected function findBySlugOr404($slug)
    {
        $appearance = $this->entityManager
            ->getRepository('KhatovarAppearanceBundle:Appearance')
            ->findOneBy(array('slug' => $slug));

        if (!$appearance) {
            throw $this->createNotFoundException('Impossible de trouver la prestation.');
        }

        return $appearance;
    }
}
