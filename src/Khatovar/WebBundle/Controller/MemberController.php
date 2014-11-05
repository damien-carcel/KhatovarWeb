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

namespace Khatovar\WebBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\WebBundle\Entity\Member;
use Khatovar\WebBundle\Form\MemberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MemberController
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Controller
 */
class MemberController extends Controller
{
    /**
     * Return the list of all members, both active or not.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()
            ->getRepository('KhatovarWebBundle:Member');

        $activeMembers = $entityManager->findBy(array('active' => true));
        $pastMembers = $entityManager->findBy(array('active' => false));

        return $this->render(
            'KhatovarWebBundle:Member:index.html.twig',
            array(
                'activeMembers' => $activeMembers,
                'pastMembers' => $pastMembers
            )
        );
    }

    /**
     * Return info about a member.
     *
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($slug)
    {
        // Sent current user ID to the view for a possible page edition
        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();

        $member = $this->getDoctrine()
            ->getRepository('KhatovarWebBundle:Member')
            ->findOneBy(array('slug' => $slug));

        return $this->render(
            'KhatovarWebBundle:Member:view.html.twig',
            array('member' => $member, 'currentUser' => $currentUser->getId())
        );
    }

    /**
     * Add a new member's page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function addAction()
    {
        $member = new Member();

        $form = $this->createForm(new MemberType(), $member);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($member);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add(
                        'notice',
                        'La page du membre ' . $member->getName()
                        . ' a bien été créée. Vous pouvez maintenant ajouter'
                        . ' des photos et choisir une photo de profil.'
                    );

                return $this->redirect(
                    $this->generateUrl('khatovar_web_members')
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Member:edit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Edit a member information.
     *
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function editAction(Member $member)
    {
        $form = $this->createForm(new MemberType(), $member);

        $form->add('portrait', 'entity', array(
                'label' => 'Photo de profil :',
                'class' => 'Khatovar\WebBundle\Entity\Photo',
                'property' => 'id'
            ));

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();
        if ($currentUser != $member->getOwner()) {
            return $this->render(
                'KhatovarWebBundle:Member:edit.html.twig',
                array(
                    'not_a_member' => 1
                )
            );
        }

        // TODO: make a custom list of member's photos, and display in the side bar the same ones
        $photos = 'test';

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($member);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Page mise à jour.');

                return $this->redirect(
                    $this->generateUrl(
                        'khatovar_web_members_view',
                        array('slug' => $member->getSlug())
                    )
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Member:edit.html.twig',
            array('form' => $form->createView(), 'photos' => $photos)
        );
    }

    /**
     * Delete a member's page.
     *
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function removeAction(Member $member)
    {
        // As it is only to delete the photo, we just need an empty form
        $form = $this->createFormBuilder()->getForm();
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($member);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Page de membre supprimée');

                return $this->redirect(
                    $this->generateUrl('khatovar_web_members')
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Member:remove.html.twig',
            array('member' => $member, 'form' => $form->createView())
        );
    }
}
