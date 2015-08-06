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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\HomepageBundle\Entity\Homepage;
use Khatovar\Bundle\HomepageBundle\Form\HomepageType;
use Khatovar\Bundle\WebBundle\Manager\PhotoManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Main Controller for Homepage bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\HomepageBundle\Controller
 */
class HomepageController extends Controller
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var PhotoManager */
    protected $photoManager;

    /** @var Session */
    protected $session;

    /**
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     * @param PhotoManager           $photoManager
     * @param Session                $session
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        PhotoManager $photoManager,
        Session $session
    ) {
        $this->container     = $container;
        $this->entityManager = $entityManager;
        $this->photoManager  = $photoManager;
        $this->session       = $session;
    }

    /**
     * Display the homepage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $homepage = $this->entityManager
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findOneBy(array('active' => true));

        if ($homepage) {
            $content = $this->photoManager->imageTranslate($homepage->getContent());
            $pageId  = $homepage->getId();
        } else {
            $content = '';
            $pageId = null;
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:index.html.twig',
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
            $this->entityManager->persist($homepage);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add('notice', 'Page d\'accueil enregistrée');

            return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:edit.html.twig',
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
            $this->entityManager->persist($homepage);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add('notice', 'Page d\'accueil modifiée');

            return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:edit.html.twig',
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
        $form = $this->createActivationForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->changeActiveHomepage($form);

            return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
        }

        $list = $this->entityManager->getRepository('KhatovarHomepageBundle:Homepage')->findAll();

        return $this->render(
            'KhatovarHomepageBundle:Homepage:list.html.twig',
            array(
                'homepage_list' => $list,
                'form'          => $form->createView()
            )
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
            $this->entityManager->remove($homepage);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add('notice', 'Page d\'accueil supprimée');

            return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:delete.html.twig',
            array(
                'homepage' => $homepage,
                'form'     => $form->createView()
            )
        );
    }

    /**
     * Create a form to activate a Homepage.
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createActivationForm()
    {
        $form = $this->createFormBuilder()
            ->add(
                'active',
                'entity',
                array(
                    'class'    => 'Khatovar\Bundle\HomepageBundle\Entity\Homepage',
                    'label'    => false,
                    'property' => 'name',
                )
            )
            ->add('submit', 'submit', array('label' => 'Activer'))
            ->getForm();

        return $form;
    }

    /**
     * Change the active Homepage.
     *
     * @param FormInterface $form
     */
    protected function changeActiveHomepage(FormInterface $form)
    {
        $repository  = $this->entityManager->getRepository('KhatovarHomepageBundle:Homepage');
        $newHomepage = $repository->find($form->get('active')->getData());
        $oldHomepage = $repository->findOneBy(array('active' => true));

        if (null !== $oldHomepage) {
            $oldHomepage->setActive(false);
            $this->entityManager->persist($oldHomepage);
        }

        $newHomepage->setActive(true);
        $this->entityManager->persist($newHomepage);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('notice', 'Page d\'accueil activée');
    }
}
