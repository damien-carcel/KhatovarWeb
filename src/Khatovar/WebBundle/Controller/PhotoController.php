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
use Khatovar\WebBundle\Entity\Photo;
use Khatovar\WebBundle\Form\PhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Photo controller. Only an user with "ROLE_EDITOR as a minimum
 * security clearance can see and manipulate photos for all the web
 * site sections. Regular users can only see their own members photos.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Controller
 */
class PhotoController extends Controller
{
    /**
     * Maximal height accepted for photo.
     */
    const HEIGHT = 720;

    /**
     * Return the list of all photos uploaded for the website and
     * display admin utilities to manage them.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function indexAction()
    {
        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        if ($currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            $entityList = array(
                'Photos orphelines' => $entityManager
                    ->getRepository('KhatovarWebBundle:Photo')
                    ->getOrphans(),
                'Pages d’accueil' => $entityManager
                    ->getRepository('KhatovarWebBundle:Homepage')
                    ->findAll(),
                'Membres' => $entityManager
                    ->getRepository('KhatovarWebBundle:Member')
                    ->findAll()
            );
        } else {
            $member = $entityManager->getRepository('KhatovarWebBundle:Member')
                ->findOneBy(array('owner' => $currentUser));

            $entityList = array(
                'Membre :' => array(
                    $member->getId() => $member
                )
            );
        }

        return $this->render(
            'KhatovarWebBundle:Photo:index.html.twig',
            array('entity_list' => $entityList)
        );
    }

    /**
     * Display a list of all photos uploaded for the current page in a
     * small sidebar. Editors and admin can access all photos, but
     * regular users can only access photos of their own member page.
     *
     * @param string $controller The controller currently rendered.
     * @param string $action The controller method used for rendering.
     * @param string|int $slug_or_id The slug or the ID of the object
     * currently rendered.
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function sideAction($controller, $action, $slug_or_id)
    {
        $photos = array();
        $currentlyRendered = null;

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        if ($controller != 'default' and $controller != 'photo') {
            $owned = $entityManager
                ->getRepository('KhatovarWebBundle:Member')
                ->findOneBy(array('owner' => $currentUser));

            $repo = $entityManager->getRepository(
                'KhatovarWebBundle:' . ucfirst($controller)
            );

            if ($controller == 'homepage'
                and is_null($slug_or_id)
                and $action != 'create'
                and $action != 'list') {
                $currentlyRendered = $repo->findOneBy(array('active' => true));
            }

            if (!is_null($slug_or_id)) {
                if (is_string($slug_or_id)) {
                    $currentlyRendered = $repo->findOneBy(array('slug' => $slug_or_id));
                } elseif (is_int($slug_or_id)) {
                    $currentlyRendered = $repo->find($slug_or_id);
                }
            }

            if (!is_null($currentlyRendered)
                and ($currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')
                or $owned->getOwner() == $currentUser)) {
                $photos = $currentlyRendered->getPhotos();
            }
        }

        return $this->render(
            'KhatovarWebBundle:Photo:side.html.twig',
            array('photos' => $photos)
        );
    }

    /**
     * Add a new photo to the collection.
     * Editors can add photos every part of the web site, but regular
     * users can only add photos for their own member page (if they
     * have one).
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function addAction(Request $request)
    {
        $photo = new Photo();

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();

        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            $member = $this->getDoctrine()->getManager()
                ->getRepository('KhatovarWebBundle:Member')
                ->findOneBy(array('owner' => $currentUser));

            if (!$member) {
                return $this->render(
                    'KhatovarWebBundle:Photo:add.html.twig',
                    array(
                        'not_a_member' => true
                    )
                );
            }

            // TODO: Is it better to use hidden fields and transmormer for Member entity?
            $photo->setClass('none')->setEntity('member')->setMember($member);

            $form = $this->createForm(new PhotoType($currentUser), $photo);
            $form->remove('class')->remove('entity')->remove('member');

            $isEditor = false;
        } else {
            $form = $this->createForm(new PhotoType($currentUser), $photo);
            $isEditor = true;
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($photo);
            $entityManager->flush();

            // We resize the uploaded photo according to the HEIGHT constant
            $resize = $this->get('khatovar.filter.resize');
            $resize->imageResize($photo->getAbsolutePath(), self::HEIGHT);

            $this->get('session')->getFlashBag()
                ->add('notice', 'Photo ajoutée');

            if ($isEditor) {
                return $this->redirect(
                    $this->generateUrl(
                        'khatovar_web_photos_edit',
                        array('photo'=> $photo->getId())
                    )
                );
            }

            // If member is a regular user, then all photo information
            // are completed during the upload, so there is no need to
            // edit it afterward.
            return $this->redirect($this->generateUrl('khatovar_web_photos'));
        }

        return $this->render(
            'KhatovarWebBundle:Photo:add.html.twig',
            array(
                'form' => $form->createView(),
                'photo' => $photo
            )
        );
    }

    /**
     * Edit a photo information.
     *
     * @param Photo $photo The photo to edit.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function editAction(Photo $photo, Request $request)
    {
        $entity = $photo->getEntity();

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();

        $form = $this->createForm(new PhotoType($currentUser), $photo);

        $member = $this->getDoctrine()->getManager()
            ->getRepository('KhatovarWebBundle:Member')
            ->findOneBy(array('owner' => $currentUser->getId()));

        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            if (!$member) {
                return $this->render(
                    'KhatovarWebBundle:Photo:add.html.twig',
                    array(
                        'not_a_member' => 1
                    )
                );
            }

            $form->remove('class')->remove('entity')->remove('member');
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($photo);

            if ($photo->getEntity() != $entity) {
                $photo->setHomepage(null)->setMember(null);

                $entityManager->flush();

                return $this->redirect(
                    $this->generateUrl(
                        'khatovar_web_photos_edit',
                        array('photo'=> $photo->getId())
                    )
                );
            } else {
                $this->get('session')->getFlashBag()
                    ->add('notice', 'Photo modifiée');

                $entityManager->flush();
            }

            return $this->redirect(
                $this->generateUrl('khatovar_web_photos')
            );
        }

        return $this->render(
            'KhatovarWebBundle:Photo:edit.html.twig',
            array(
                'photo' => $photo,
                'form' => $form->createView(),
                'owner' => $member
            )
        );
    }

    /**
     * Remove a photo from the collection.
     *
     * @param Photo $photo The photo to delete.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function deleteAction(Photo $photo, Request $request)
    {
        // As it is only to delete the photo, we just need an empty form
        $form = $this->createFormBuilder()->getForm();

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();
        $member = $this->getDoctrine()->getManager()
            ->getRepository('KhatovarWebBundle:Member')
            ->findOneBy(array('owner' => $currentUser->getId()));

        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR') and !$member) {
            // If the user doesn't have a member's page, then he have no
            // reason to delete photos
            return $this->render(
                'KhatovarWebBundle:Photo:delete.html.twig',
                array(
                    'not_an_editor' => 1
                )
            );
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($photo);
            $entityManager->flush();

            $this->get('session')->getFlashBag()
                ->add('notice', 'Photo supprimée');

            return $this->redirect(
                $this->generateUrl('khatovar_web_photos')
            );
        }

        return $this->render(
            'KhatovarWebBundle:Photo:delete.html.twig',
            array(
                'photo' => $photo,
                'form' => $form->createView(),
                'owner' => $member
            )
        );
    }
}
