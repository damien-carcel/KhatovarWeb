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

use Doctrine\ORM\EntityManagerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\HomepageBundle\Entity\Homepage;
use Khatovar\Bundle\HomepageBundle\Form\HomepageType;
use Khatovar\Bundle\PhotoBundle\Manager\PhotoManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Main Controller for Homepage bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
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
     * Displays the active homepage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $homepage = $this->findActiveOr404();

        return $this->render(
            'KhatovarHomepageBundle:Homepage:show.html.twig',
            array(
                'content' => $this->photoManager->imageTranslate($homepage->getContent()),
                'page_id' => $homepage->getId()
            )
        );
    }

    /**
     * Finds and display a homepage.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $homepage = $this->findByIdOr404($id);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:show.html.twig',
            array(
                'content' => $this->photoManager->imageTranslate($homepage->getContent()),
                'page_id' => $homepage->getId()
            )
        );
    }

    /**
     * List of all homepages, and allow to activate one of them.
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

            $this->session->getFlashBag()->add(
                'notice',
                'Page d\'accueil activée'
            );

            return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
        }

        $homepages = $this->entityManager->getRepository('KhatovarHomepageBundle:Homepage')->findAll();
        $deleteForms = $this->createDeleteForms($homepages);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:list.html.twig',
            array(
                'homepages'       => $homepages,
                'activation_form' => $form->createView(),
                'delete_forms'    => $deleteForms,
            )
        );
    }

    /**
     * Displays a form to create a new homepage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function newAction()
    {
        $homepage = new Homepage();

        $form = $this->createCreateForm($homepage);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:new.html.twig',
            array('form' => $form->createView(),)
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

        $form = $this->createCreateForm($homepage);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->persist($homepage);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Page d\'accueil créée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_homepage_show',
                    array('id' => $homepage->getId())
                )
            );
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Displays a form to edit an existing homepage.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction($id)
    {
        $homepage = $this->findByIdOr404($id);

        $editForm = $this->createEditForm($homepage);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:edit.html.twig',
            array('edit_form' => $editForm->createView(),)
        );
    }

    /**
     * Edits an existing homepage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function updateAction(Request $request, $id)
    {
        $homepage = $this->findByIdOr404($id);

        $form = $this->createEditForm($homepage);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->flush();

            $this->session->getFlashBag()->add(
                'notice',
                'Page d\'accueil modifiée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_homepage_show',
                    array('id' => $id)
                )
            );
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:edit.html.twig',
            array('edit_form' => $form->createView())
        );
    }

    /**
     * Deletes a homepage.
     *
     * @param Request $request
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Request $request, $id)
    {
        $homepage = $this->findByIdOr404($id);

        if ($homepage->isActive()) {
            $this->session->getFlashBag()->add(
                'notice',
                'Vous ne pouvez pas supprimer la page d\'accueil active'
            );
        } else {
            $form = $this->createDeleteForm($id);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->entityManager->remove($homepage);
                $this->entityManager->flush();

                $this->session->getFlashBag()->add(
                    'notice',
                    'Page d\'accueil supprimée'
                );
            }
        }

        return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
    }

    /**
     * Creates a form to create a Homepage entity.
     *
     * @param Homepage $homepage
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createCreateForm(Homepage $homepage)
    {
        $form = $this->createForm(
            new HomepageType(),
            $homepage,
            array(
                'action' => $this->generateUrl('khatovar_web_homepage_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Créer'));

        return $form;
    }

    /**
     * Creates a form to edit a Homepage entity.
     *
     * @param Homepage $homepage
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createEditForm(Homepage $homepage)
    {
        $form = $this->createForm(
            new HomepageType(),
            $homepage,
            array(
                'action' => $this->generateUrl('khatovar_web_homepage_update', array('id' => $homepage->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Mettre à jour'));

        return $form;
    }

    /**
     * Creates a form to delete a Homepage entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_homepage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit',
                array(
                    'label' => 'Effacer',
                    'attr'  => array('onclick' => 'return confirm("Êtes-vous sûr ?")'),
                )
            )
            ->getForm();
    }

    /**
     * Return a list of delete forms for a set of Homepage entities.
     *
     * @param Homepage[] $homepages
     *
     * @return \Symfony\Component\Form\Form[]
     */
    protected function createDeleteForms(array $homepages)
    {
        $deleteForms = array();

        foreach ($homepages as $homepage) {
            $deleteForms[$homepage->getId()] = $this->createDeleteForm($homepage->getId())->createView();
        }

        return $deleteForms;
    }

    /**
     * @param int $id
     *
     * @return Homepage
     */
    protected function findByIdOr404($id)
    {
        $homepage = $this->entityManager->getRepository('KhatovarHomepageBundle:Homepage')->find($id);

        if (!$homepage) {
            throw $this->createNotFoundException('Impossible de trouver la page d\'accueil.');
        }

        return $homepage;
    }

    /**
     * @return Homepage
     */
    protected function findActiveOr404()
    {
        $homepage = $this->entityManager
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findOneBy(array('active' => true));

        if (null === $homepage) {
            throw new NotFoundHttpException('There is no active Contact entity. You must activate one.');
        }

        return $homepage;
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
