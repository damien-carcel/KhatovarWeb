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
use Khatovar\WebBundle\Entity\Homepage;
use Khatovar\WebBundle\Form\HomepageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class HomepageController
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Controller
 */
class HomepageController extends Controller
{
    /**
     * Display the homepage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $homepage = $this->getDoctrine()->getManager()
            ->getRepository('KhatovarWebBundle:Homepage')
            ->findOneBy(array('active' => true));

        $translations = $this->get('khatovar.filters.translation');

        return $this->render(
            'KhatovarWebBundle:Accueil:index.html.twig',
            array(
                'content' => $translations->imageTranslate(
                    $homepage->getContent()
                ),
                'page_id' => $homepage->getId()
            )
        );
    }

    /**
     * Create a new homepage.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function createAction()
    {
        $homepage = new Homepage();

        $form = $this->createForm(new HomepageType(), $homepage);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($homepage);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Page d’accueil enregistrée');

                return $this->redirect(
                    $this->generateUrl('khatovar_web_homepage_list')
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Accueil:edit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Edit an existing homepage.
     *
     * @param Homepage $homepage
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction(Homepage $homepage)
    {
        $form = $this->createForm(new HomepageType(), $homepage);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($homepage);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Page d’accueil modifiée');

                return $this->redirect(
                    $this->generateUrl('khatovar_web_homepage_list')
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Accueil:edit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Return a list of all Homepage stored in database, and allow to
     * activate one of them.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function listAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('KhatovarWebBundle:Homepage');

        $old_homepage = $repository->findOneBy(array('active' => true));

        $form = $this->createFormBuilder()
            ->add('active', 'entity', array(
                    'class' => 'Khatovar\WebBundle\Entity\Homepage',
                    'label' => false,
                    'property' => 'name',
                    'preferred_choices' => array($old_homepage)
                ))
            ->add('submit', 'submit', array('label' => 'Activer'))
            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $new_homepage = $repository
                    ->find($form->get('active')->getData());

                if ($old_homepage->getId() != $new_homepage->getId()) {
                    // Todo: Understand why I have to use these two lines
                    $old_homepage->setActive(false);
                    $new_homepage->setActive(true);

                    $entityManager->persist($old_homepage);
                    $entityManager->persist($new_homepage);
                    $entityManager->flush();

                    $this->get('session')->getFlashBag()
                        ->add('notice', 'Page d’accueil activée');
                }

                return $this->redirect(
                    $this->generateUrl('khatovar_web_homepage_list')
                );
            }
        }

        // We get all the homepage in case we want to edit another one
        // that the active one.
        $list = $repository->findAll();

        return $this->render(
            'KhatovarWebBundle:Accueil:list.html.twig',
            array('homepage_list' => $list, 'form' => $form->createView())
        );
    }

    /**
     * Delete a homepage.
     *
     * @param Homepage $homepage
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Homepage $homepage)
    {
        // As it is only to delete the photo, we just need an empty form
        $form = $this->createFormBuilder()->getForm();
        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($homepage);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Page d’accueil supprimée');

                return $this->redirect(
                    $this->generateUrl('khatovar_web_homepage_list')
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Accueil:delete.html.twig',
            array('homepage' => $homepage, 'form' => $form->createView())
        );
    }
}
