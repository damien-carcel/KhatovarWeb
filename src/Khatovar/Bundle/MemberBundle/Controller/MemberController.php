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

namespace Khatovar\Bundle\MemberBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\MemberBundle\Entity\Member;
use Khatovar\Bundle\MemberBundle\Form\MemberType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Main Controller for Member bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class MemberController extends Controller
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Session */
    protected $session;

    /**
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     * @param Session                $session
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $entityManager,
        Session $session
    ) {
        $this->container     = $container;
        $this->entityManager = $entityManager;
        $this->session       = $session;

    }

    /**
     * Return the list of all members, both active or not.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $memberRepository  = $this->entityManager->getRepository('KhatovarMemberBundle:Member');
        $activeMembers     = $memberRepository->findBy(array('active' => true));
        $pastMembers       = $memberRepository->findBy(array('active' => false));
        $activeDeleteForms = $this->createDeleteForms($activeMembers);
        $pastDeleteForms   = $this->createDeleteForms($pastMembers);

        return $this->render(
            'KhatovarMemberBundle:Member:index.html.twig',
            array(
                'active_members'      => $activeMembers,
                'past_members'        => $pastMembers,
                'active_delete_forms' => $activeDeleteForms,
                'past_delete_forms'   => $pastDeleteForms,
            )
        );
    }

    /**
     * Finds and displays a member page.
     *
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slug)
    {
        $member = $this->findBySlugOr404($slug);

        $currentUser = $this->getUser();

        $photos = $this->entityManager
            ->getRepository('KhatovarPhotoBundle:Photo')
            ->getAllButPortrait($member);

        return $this->render(
            'KhatovarMemberBundle:Member:show.html.twig',
            array(
                'member'       => $member,
                'current_user' => $currentUser,
                'photos'       => $photos,
            )
        );
    }

    /**
     * Displays a form to create a new member page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function newAction()
    {
        $member = new Member();

        $form = $this->createCreateForm($member);

        return $this->render(
            'KhatovarMemberBundle:Member:new.html.twig',
            array(
                'form' => $form->createView(),
                'edit' => false,
            )
        );
    }

    /**
     * Creates a new member page.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function createAction(Request $request)
    {
        $member = new Member();

        $form = $this->createCreateForm($member);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->persist($member);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(
                    'La page du membre %s a bien été créée. Vous pouvez maintenant ajouter une photo de profil.',
                    $member->getName()
                )
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_member_show',
                    array('slug' => $member->getSlug())
                )
            );
        }

        return $this->render(
            'KhatovarMemberBundle:Member:new.html.twig',
            array(
                'form' => $form->createView(),
                'edit' => false,
            )
        );
    }

    /**
     * Displays a form to edit an existing member page.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_EDITOR")
     */
    public function editAction($id)
    {
        $contact = $this->findByIdOr404($id);

        $editForm = $this->createEditForm($contact);

        return $this->render(
            'KhatovarMemberBundle:Member:edit.html.twig',
            array(
                'edit_form' => $editForm->createView(),
                'edit'      => true,
            )
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
     * @Secure(roles="ROLE_VIEWER")
     */
    public function updateAction(Request $request, $id)
    {
        $member = $this->findByIdOr404($id);

        $editForm = $this->createEditForm($member);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Page de membre modifiée'
            );

            return $this->redirect(
                $this->generateUrl(
                    'khatovar_web_member_show',
                    array('slug' => $member->getSlug())
                )
            );
        }

        return $this->render(
            'KhatovarMemberBundle:Member:edit.html.twig',
            array(
                'edit_form' => $editForm->createView(),
                'edit'      => true,
            )
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
     * @Secure(roles="ROLE_EDITOR")
     */
    public function deleteAction(Request $request, $id)
    {
        $member = $this->findByIdOr404($id);

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->entityManager->remove($member);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Page de membre supprimée'
            );
        }

        return $this->redirect($this->generateUrl('khatovar_web_member'));
    }

    /**
     * Creates a form to create a Member entity.
     *
     * @param Member $member
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm(Member $member)
    {
        $form = $this->createForm(
            new MemberType(),
            $member,
            array(
                'action' => $this->generateUrl('khatovar_web_member_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Créer'));

        return $form;
    }

    /**
     * Creates a form to edit a Member entity.
     *
     * @param Member $member
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(Member $member)
    {
        $form = $this->createForm(
            new MemberType(),
            $member,
            array(
                'action' => $this->generateUrl('khatovar_web_member_update', array('id' => $member->getId())),
                'method' => 'PUT',
            )
        );

        $form->add(
            'portrait',
            'entity',
            array(
                'label' => 'Photo de profil',
                'class' => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                'property' => 'alt',
                'query_builder' => function (EntityRepository $er) use ($member) {
                    return $er->createQueryBuilder('p')
                        ->where('p.member = ?1')
                        ->setParameter(1, $member);
                }
            )
        );

        $currentUser = $this->getUser();
        if (!$this->isGranted('ROLE_EDITOR')) {
            if ($currentUser != $member->getOwner()) {
                throw new AccessDeniedHttpException(
                    'Désolé, ceci n\'est pas votre page de membre. Vous ne pouvez donc pas la modifier.'
                );
            }

            $form->remove('owner');
        }

        $form->add('submit', 'submit', array('label' => 'Mettre à jour'));

        return $form;
    }

    /**
     * Creates a form to delete a Member entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id)
    {
        return $this
            ->createFormBuilder()
            ->setAction($this->generateUrl('khatovar_web_member_delete', array('id' => $id)))
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
     * Return a list of delete forms for a set of Member entities.
     *
     * @param Member[] $members
     *
     * @return \Symfony\Component\Form\Form[]
     */
    protected function createDeleteForms(array $members)
    {
        $deleteForms = array();

        foreach ($members as $member) {
            $deleteForms[$member->getId()] = $this->createDeleteForm($member->getId())->createView();
        }

        return $deleteForms;
    }

    /**
     * @param int $id
     *
     * @return Member
     */
    protected function findByIdOr404($id)
    {
        $contact = $this->entityManager->getRepository('KhatovarMemberBundle:Member')->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Impossible de trouver le membre.');
        }

        return $contact;
    }

    /**
     * @param string $slug
     *
     * @return Member
     */
    protected function findBySlugOr404($slug)
    {
        $contact = $this->entityManager
            ->getRepository('KhatovarMemberBundle:Member')
            ->findOneBy(array('slug' => $slug));

        if (!$contact) {
            throw $this->createNotFoundException('Impossible de trouver le membre.');
        }

        return $contact;
    }
}
