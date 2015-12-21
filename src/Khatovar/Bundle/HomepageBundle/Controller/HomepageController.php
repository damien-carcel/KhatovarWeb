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

use Khatovar\Bundle\HomepageBundle\Entity\Homepage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Main Controller for Homepage bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class HomepageController extends Controller
{
    /**
     * Displays the active homepage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $homepage = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findActiveOr404();

        return $this->render(
            'KhatovarHomepageBundle:Homepage:show.html.twig',
            [
                'content' => $this->get('khatovar_photo.manager.photo')->imageTranslate($homepage->getContent()),
                'page_id' => $homepage->getId()
            ]
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
        $homepage = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findByIdOr404($id);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:show.html.twig',
            [
                'content' => $this->get('khatovar_photo.manager.photo')->imageTranslate($homepage->getContent()),
                'page_id' => $homepage->getId()
            ]
        );
    }

    /**
     * List of all homepages, and allows to activate one of them.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function listAction(Request $request)
    {
        $form = $this->createActivationForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->changeActiveHomepage($form);

            $this->addFlash(
                'notice',
                'Page d\'accueil activée'
            );

            return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
        }

        $homepages = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findAll();

        $deleteForms = $this->createDeleteForms($homepages);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:list.html.twig',
            [
                'homepages'       => $homepages,
                'activation_form' => $form->createView(),
                'delete_forms'    => $deleteForms,
            ]
        );
    }

    /**
     * Displays a form to create a new homepage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function newAction()
    {
        $homepage = new Homepage();
        $form     = $this->createCreateForm($homepage);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Create a new homepage.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function createAction(Request $request)
    {
        $homepage = new Homepage();

        $form = $this->createCreateForm($homepage);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->persist($homepage);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Page d\'accueil créée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_homepage_show',
                    ['id' => $homepage->getId()]
                )
            );
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing homepage.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function editAction($id)
    {
        $homepage = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findByIdOr404($id);

        $editForm = $this->createEditForm($homepage);

        return $this->render(
            'KhatovarHomepageBundle:Homepage:edit.html.twig',
            ['edit_form' => $editForm->createView()]
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
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function updateAction(Request $request, $id)
    {
        $homepage = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findByIdOr404($id);

        $form = $this->createEditForm($homepage);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('doctrine.orm.entity_manager')->flush();

            $this->addFlash(
                'notice',
                'Page d\'accueil modifiée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_homepage_show',
                    ['id' => $id]
                )
            );
        }

        return $this->render(
            'KhatovarHomepageBundle:Homepage:edit.html.twig',
            ['edit_form' => $form->createView()]
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
     * @Security("has_role('ROLE_EDITOR')")
     */
    public function deleteAction(Request $request, $id)
    {
        $homepage = $this->get('doctrine.orm.entity_manager')
            ->getRepository('KhatovarHomepageBundle:Homepage')
            ->findByIdOr404($id);

        if ($homepage->isActive()) {
            $this->addFlash(
                'notice',
                'Vous ne pouvez pas supprimer la page d\'accueil active'
            );
        } else {
            $form = $this->createDeleteForm($id);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->get('doctrine.orm.entity_manager');
                $entityManager->remove($homepage);
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Page d\'accueil supprimée'
                );
            }
        }

        return $this->redirect($this->generateUrl('khatovar_web_homepage_list'));
    }

    /**
     * Create a form to activate a Homepage.
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createActivationForm()
    {
        $form = $this->createForm(
            'Khatovar\Bundle\HomepageBundle\Form\Type\HomepageActivationType',
            null,
            [
                'action' => $this->generateUrl('khatovar_web_homepage_list'),
                'method' => 'PUT',
            ]
        );

        return $form;
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
            'Khatovar\Bundle\HomepageBundle\Form\Type\HomepageType',
            $homepage,
            [
                'action' => $this->generateUrl('khatovar_web_homepage_create'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Créer']);

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
            'Khatovar\Bundle\HomepageBundle\Form\Type\HomepageType',
            $homepage,
            [
                'action' => $this->generateUrl('khatovar_web_homepage_update', ['id' => $homepage->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Mettre à jour']);

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
            ->setAction($this->generateUrl('khatovar_web_homepage_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Effacer',
                    'attr'  => ['onclick' => 'return confirm("Êtes-vous sûr ?")'],
                ]
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
        $deleteForms = [];

        foreach ($homepages as $homepage) {
            $deleteForms[$homepage->getId()] = $this->createDeleteForm($homepage->getId())->createView();
        }

        return $deleteForms;
    }

    /**
     * Change the active Homepage.
     *
     * @param FormInterface $form
     *
     * @todo Use a handler (check if the one of Contact entity is reusable).
     */
    protected function changeActiveHomepage(FormInterface $form)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $repository  = $entityManager->getRepository('KhatovarHomepageBundle:Homepage');
        $newHomepage = $repository->find($form->get('active')->getData());
        $oldHomepage = $repository->findOneBy(['active' => true]);

        if (null !== $oldHomepage) {
            $oldHomepage->setActive(false);
            $entityManager->persist($oldHomepage);
        }

        $newHomepage->setActive(true);
        $entityManager->persist($newHomepage);
        $entityManager->flush();

        $this->addFlash('notice', 'Page d\'accueil activée');
    }
}
