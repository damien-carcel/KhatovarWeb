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

use Khatovar\Bundle\WebBundle\Entity\Member;
use Khatovar\Bundle\WebBundle\Form\Type\MemberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MemberController extends Controller
{
    /**
     * Displays a form to create a new member page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction()
    {
        $member = new Member();
        $form = $this->createCreateForm($member);

        return $this->render(
            'KhatovarWebBundle:Member:new.html.twig',
            [
                'form' => $form->createView(),
                'edit' => false,
            ]
        );
    }

    /**
     * Creates a new member page.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function createAction(Request $request)
    {
        $member = new Member();

        $form = $this->createCreateForm($member);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->persist($member);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                sprintf(
                    'La page du membre %s a bien été créée. Vous pouvez maintenant ajouter une photo de profil.',
                    $member->getName()
                )
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_member_show',
                    ['slug' => $member->getSlug()]
                )
            );
        }

        return $this->render(
            'KhatovarWebBundle:Member:new.html.twig',
            [
                'form' => $form->createView(),
                'edit' => false,
            ]
        );
    }

    /**
     * Displays a form to edit an existing member page.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_VIEWER')")
     */
    public function editAction($id)
    {
        $contact = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Member')
            ->findByIdOr404($id);

        $editForm = $this->createEditForm($contact);

        return $this->render(
            'KhatovarWebBundle:Member:edit.html.twig',
            [
                'edit_form' => $editForm->createView(),
                'edit' => true,
            ]
        );
    }

    /**
     * Updates a member page.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_VIEWER')")
     */
    public function updateAction(Request $request, $id)
    {
        $member = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Member')
            ->findByIdOr404($id);

        $editForm = $this->createEditForm($member);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->get('doctrine')->getManager()->flush();

            $this->addFlash('notice', 'Page de membre modifiée');

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_member_show',
                    ['slug' => $member->getSlug()]
                )
            );
        }

        return $this->render(
            'KhatovarWebBundle:Member:edit.html.twig',
            [
                'edit_form' => $editForm->createView(),
                'edit' => true,
            ]
        );
    }

    /**
     * Delete a member page.
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
        $member = $this->get('doctrine')
            ->getRepository('KhatovarWebBundle:Member')
            ->findByIdOr404($id);

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->remove($member);
            $entityManager->flush();

            $this->addFlash('notice', 'Page de membre supprimée');
        }

        return $this->redirect($this->generateUrl('khatovar_web_member'));
    }

    /**
     * Creates a form to create a Member entity.
     *
     * @param Member $member
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createCreateForm(Member $member)
    {
        $form = $this->createForm(
            MemberType::class,
            $member,
            [
                'action' => $this->generateUrl('khatovar_web_member_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Créer']);

        return $form;
    }

    /**
     * Creates a form to edit a Member entity.
     *
     * @param Member $member
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(Member $member)
    {
        if (!$this->isGranted('ROLE_EDITOR') && $this->getUser() !== $member->getOwner()) {
            throw new AccessDeniedException(
                'Désolé, ceci n\'est pas votre page de membre. Vous ne pouvez donc pas la modifier.'
            );
        }

        $form = $this->createForm(
            MemberType::class,
            $member,
            [
                'action' => $this->generateUrl('khatovar_web_member_update', ['id' => $member->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Mettre à jour']);

        return $form;
    }

    /**
     * Creates a form to delete a Member entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_member_delete', ['id' => $id]))
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
     * Return a list of delete forms for a set of Member entities.
     *
     * @param Member[] $members
     *
     * @return \Symfony\Component\Form\FormInterface[]
     */
    protected function createDeleteForms(array $members)
    {
        $deleteForms = [];

        foreach ($members as $member) {
            $deleteForms[$member->getId()] = $this->createDeleteForm($member->getId())->createView();
        }

        return $deleteForms;
    }
}
