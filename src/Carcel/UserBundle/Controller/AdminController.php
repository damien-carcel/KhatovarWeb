<?php
/**
 *
 * This file is part of Documents.
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
 * @link        https://github.com/damien-carcel/Documents
 * @license     http://www.gnu.org/licenses/gpl.html
 * @todo        Externalising email sending when removing a user account
 */

namespace Carcel\UserBundle\Controller;

use Carcel\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Global adminitration of the application users.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Carcel\UserBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * Return a list of all the application users, except the SUPER_ADMIN.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $superAdmin = $this->get('security.context')->getToken()->getUser();
        $users = $this->getDoctrine()->getManager()
            ->getRepository('CarcelUserBundle:User')
            ->getAllBut($superAdmin->getId());

        return $this->render(
            'CarcelUserBundle:Admin:index.html.twig',
            array('users' => $users)
        );
    }

    /**
     * Show a users's profile.
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showProfileAction(User $user)
    {
        return $this->render('CarcelUserBundle:Admin:show.html.twig', array('user' => $user));
    }

    /**
     * Change the user's role.
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function setRoleAction(User $user)
    {
        // Get all roles available in the application
        $roles = $this->container->getParameter('security.role_hierarchy.roles');
        // And also the actual user's role
        $userRoles = $user->getRoles();
        $choices['ROLE_USER'] = 'ROLE_USER';
        foreach ($roles as $key => $role) {
            if ($key != 'ROLE_SUPER_ADMIN') {
                $choices[$key] = $key;
            }
            if ($role[0] == $userRoles[0]) {
                $data = $role[0];
            }
        }

        $form = $this->createFormBuilder()
            ->add(
                'roles',
                'choice',
                array(
                    'choices' => $choices,
                    'label' => false,
                    'data' => $data
                )
            )
            ->add('submit', 'submit', array('label' => 'Modifier'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $retrieved = $form->getData('roles');
                $user->setRoles(array($choices[$retrieved['roles']]));
                $entityManager->persist($user);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Le rôle de l’utilisateur a été modifié.');

                return $this->redirect($this->generateUrl('carcel_user_admin'));
            }
        }

        return $this->render(
            'CarcelUserBundle:Admin:set_role.html.twig',
            array(
                'form' => $form->createView(),
                'username' => $user->getUsername()
            )
        );
    }

    /**
     * Edit a user's profile.
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProfileAction(User $user)
    {
        $form = $this->createFormBuilder($user)
            ->add('username', 'text', array('label' => 'Nom d’utilisateur :'))
            ->add('email', 'email', array('label' => 'Adresse e-mail :'))
            ->add('submit', 'submit', array('label' => 'Mettre à jour'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Le profile utilisateur a été mis à jour.');

                return $this->redirect($this->generateUrl('carcel_user_admin'));
            }
        }

        return $this->render(
            'CarcelUserBundle:Admin:edit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Remove a user account and send a message to warn the user that
     * his account has been destroyed.
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function removeUserAction(User $user)
    {
        $form = $this->createFormBuilder()->getForm();
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $email = $user->getEmail();
                $username = $user->getUsername();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'L’utilisateur a bien été effacé.');

                $message = \Swift_Message::newInstance()
                    ->setSubject('Suppression de compte')
                    ->setFrom('registration@documents.com')
                    ->setTo($email)
                    ->setBody(
                        $this->renderView(
                            'CarcelUserBundle:Admin:email.txt.twig',
                            array('username' => $username)
                        )
                    );
                $this->get('mailer')->send($message);

                return $this->redirect($this->generateUrl('carcel_user_admin'));
            }
        }

        return $this->render(
            'CarcelUserBundle:Admin:remove.html.twig',
            array('form' => $form->createView(), 'user' => $user)
        );
    }
}
