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

namespace Khatovar\Bundle\HomepageBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\HomepageBundle\Entity\Homepage;
use Khatovar\Bundle\HomepageBundle\Form\HomepageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HomepageController
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\HomepageBundle\Controller
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
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findOneBy(array('active' => true));

        $translations = $this->get('khatovar.filters.translation');

        if ($homepage) {
            $content = $translations->imageTranslate($homepage->getContent());
            $pageId = $homepage->getId();
        } else {
            $content = '';
            $pageId = null;
        }

        return $this->render(
            'KhatovarHomepageBundle:Accueil:index.html.twig',
            array(
                'content' => $content,
                'page_id' => $pageId
            )
        );
    }

    /**
     * Create a new homepage.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function createAction(Request $request)
    {
        $homepage = new Homepage();

        $form = $this->createForm(new HomepageType(), $homepage);

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

        return $this->render(
            'KhatovarWebBundle:Accueil:edit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Edit an existing homepage.
     *
     * @param Homepage $homepage
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction(Homepage $homepage, Request $request)
    {
        $form = $this->createForm(new HomepageType(), $homepage);

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

        return $this->render(
            'KhatovarWebBundle:Accueil:edit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Return a list of all Homepage stored in database, and allow to
     * activate one of them.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function listAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository('KhatovarWebBundle:Homepage');

        $oldHomepage = $repository->findOneBy(array('active' => true));

        if (!$oldHomepage) {
            $oldHomepage = new Homepage();
        }
        $form = $this->createFormBuilder()
            ->add('active', 'entity', array(
                    'class' => 'Khatovar\WebBundle\Entity\Homepage',
                    'label' => false,
                    'property' => 'name',
                    'preferred_choices' => array($oldHomepage)
                ))
            ->add('submit', 'submit', array('label' => 'Activer'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $new_homepage = $repository
                ->find($form->get('active')->getData());

            // Todo: Understand why I have to manually setActive
            if ($oldHomepage->getId() != $new_homepage->getId()) {
                if ($oldHomepage->getId()) {
                    $oldHomepage->setActive(false);
                    $entityManager->persist($oldHomepage);
                }

                $new_homepage->setActive(true);
                $entityManager->persist($new_homepage);
                $entityManager->flush();

                $this->get('session')->getFlashBag()
                    ->add('notice', 'Page d’accueil activée');
            }

            return $this->redirect(
                $this->generateUrl('khatovar_web_homepage_list')
            );
        }

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
     * @param Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Homepage $homepage, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
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

        return $this->render(
            'KhatovarWebBundle:Accueil:delete.html.twig',
            array('homepage' => $homepage, 'form' => $form->createView())
        );
    }
}
