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

use Khatovar\Bundle\WebBundle\Entity\Contact;
use Khatovar\Bundle\WebBundle\Form\Type\ContactActivationType;
use Khatovar\Bundle\WebBundle\Form\Type\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ContactController extends Controller
{
    /**
     * Lists all contact pages, and allows to activate one of them.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function listAction(Request $request)
    {
        $form = $this->createForm(ContactActivationType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('khatovar_contact.handler.contact_activation')->handle($form->get('active')->getData());

            $this->addFlash('notice', 'Page de contact activée');

            return $this->redirect($this->generateUrl('khatovar_web_contact_list'));
        }

        $contacts = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Contact')
            ->findAll();

        $deleteForms = $this->createDeleteForms($contacts);

        return $this->render(
            'KhatovarWebBundle:Contact:list.html.twig',
            [
                'contacts' => $contacts,
                'activation_form' => $form->createView(),
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * Displays a form to create a new contact page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction()
    {
        $contact = new Contact();

        $form = $this->createCreateForm($contact);

        return $this->render(
            'KhatovarWebBundle:Contact:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Creates a new contact page.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->createCreateForm($contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('notice', 'Page de contact créée');

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_contact_show',
                    ['id' => $contact->getId()]
                )
            );
        }

        return $this->render(
            'KhatovarWebBundle:Contact:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing contact page.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function editAction($id)
    {
        $contact = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Contact')
            ->findByIdOr404($id);

        $editForm = $this->createEditForm($contact);

        return $this->render(
            'KhatovarWebBundle:Contact:edit.html.twig',
            ['edit_form' => $editForm->createView()]
        );
    }

    /**
     * Updates a contact page.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function updateAction(Request $request, $id)
    {
        $contact = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Contact')
            ->findByIdOr404($id);

        $editForm = $this->createEditForm($contact);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->get('doctrine')->getManager()->flush();

            $this->addFlash('notice', 'Page de contact modifiée');

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_contact_show',
                    ['id' => $id]
                )
            );
        }

        return $this->render(
            'KhatovarWebBundle:Contact:edit.html.twig',
            ['edit_form' => $editForm->createView()]
        );
    }

    /**
     * Deletes a contact page.
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
        $contact = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Contact')
            ->findByIdOr404($id);

        if ($contact->isActive()) {
            $this->addFlash('notice', 'Vous ne pouvez pas supprimer la page d\'accueil active');
        } else {
            $form = $this->createDeleteForm($id);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->get('doctrine')->getManager();
                $entityManager->remove($contact);
                $entityManager->flush();

                $this->addFlash('notice', 'Page de contact supprimée');
            }
        }

        return $this->redirect($this->generateUrl('khatovar_web_contact_list'));
    }

    /**
     * Creates a form to create a Contact entity.
     *
     * @param Contact $contact
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createCreateForm(Contact $contact)
    {
        $form = $this->createForm(
            ContactType::class,
            $contact,
            [
                'action' => $this->generateUrl('khatovar_web_contact_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Créer']);

        return $form;
    }

    /**
     * Creates a form to edit a Contact entity.
     *
     * @param Contact $contact
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(Contact $contact)
    {
        $form = $this->createForm(
            ContactType::class,
            $contact,
            [
                'action' => $this->generateUrl('khatovar_web_contact_update', ['id' => $contact->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Mettre à jour']);

        return $form;
    }

    /**
     * Creates a form to delete a Contact entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_contact_delete', ['id' => $id]))
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
     * Return a list of delete forms for a set of Contact entities.
     *
     * @param Contact[] $contacts
     *
     * @return \Symfony\Component\Form\FormInterface[]
     */
    protected function createDeleteForms(array $contacts)
    {
        $deleteForms = [];

        foreach ($contacts as $contact) {
            $deleteForms[$contact->getId()] = $this->createDeleteForm($contact->getId())->createView();
        }

        return $deleteForms;
    }
}
