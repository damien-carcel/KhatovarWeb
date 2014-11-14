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

use Doctrine\ORM\EntityRepository;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\WebBundle\Entity\Member;
use Khatovar\WebBundle\Form\MemberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @ParamConverter("member", options={"mapping": {"member_slug": "slug"}})
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Member $member)
    {
        // Sent current user ID to the view for a possible page edition
        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();

        // Get all but the portrait photos
        $photos = $this->getDoctrine()
            ->getRepository('KhatovarWebBundle:Photo')
            ->getAllButPortrait($member);

        return $this->render(
            'KhatovarWebBundle:Member:view.html.twig',
            array(
                'member' => $member,
                'currentUser' => $currentUser,
                'photos' => $photos
            )
        );
    }

    /**
     * Add a new member's page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function editAction(Member $member)
    {
        $form = $this->createForm(new MemberType(), $member);

        $form->add('portrait', 'entity', array(
                'label' => 'Photo de profil :',
                'class' => 'Khatovar\WebBundle\Entity\Photo',
                'property' => 'id',
                'query_builder' => function (EntityRepository $er) use ($member) {
                    return $er->createQueryBuilder('p')
                        ->where('p.entry = ?1')
                        ->setParameter(1, $member->getId());
                }
            ));

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();
        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            $form->remove('owner');
            if ($currentUser != $member->getOwner()) {
                return $this->render(
                    'KhatovarWebBundle:Member:edit.html.twig',
                    array(
                        'not_a_member' => 1
                    )
                );
            }
        }

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
                        array('member_slug' => $member->getSlug())
                    )
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Member:edit.html.twig',
            array('form' => $form->createView(), 'edit' => 'is_defined')
        );
    }

    /**
     * Delete a member's page.
     *
     * @param Member $member
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
