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

namespace Khatovar\Bundle\PhotoBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Khatovar\Bundle\PhotoBundle\Entity\Photo;
use Khatovar\Bundle\PhotoBundle\Form\PhotoType;
use Khatovar\Bundle\PhotoBundle\Manager\PhotoManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Main controller for Photo bundle.
 *
 * Only a user with "ROLE_EDITOR as a minimum
 * security clearance can see and manipulate photos for all the web
 * site sections. Regular users can only see their own members photos.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class PhotoController extends Controller
{
    /** @staticvar string */
    const MAX_PHOTO_HEIGHT = 720;

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
     * Return the list of all photos uploaded for the website and
     * display admin utilities to manage them.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_VIEWER")
     */
    public function indexAction()
    {
        if ($this->isGranted('ROLE_EDITOR')) {
            $entityList = $this->photoManager->getPhotoEntitiesList();
        } else {
            $entityList = $this->photoManager->getUserPhotos($this->getUser());
        }

        return $this->render(
            'KhatovarPhotoBundle:Photo:index.html.twig',
            array('entity_list' => $entityList)
        );
    }

    /**
     * Display a list of all photos uploaded for the current page in a
     * small sidebar. Editors and admin can access all photos, but
     * regular users can only access photos of their own member page.
     *
     * @param string     $controller The controller currently rendered.
     * @param string     $action     The controller method used for rendering.
     * @param string|int $slugOrId   The slug or the ID of the object currently rendered.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_VIEWER")
     */
    public function sideAction($controller, $action, $slugOrId)
    {
        $photos = $this->photoManager->getControllerPhotos($this->getUser(), $controller, $action, $slugOrId);

        return $this->render(
            'KhatovarPhotoBundle:Photo:side.html.twig',
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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_VIEWER")
     */
    public function addAction(Request $request)
    {
        $photo = new Photo();

        if (!$this->isGranted('ROLE_EDITOR')) {
            $member = $this->getLoggedMember();
            if (!$member) {
                return $this->render(
                    'KhatovarPhotoBundle:Photo:add.html.twig',
                    array('not_a_member' => true)
                );
            }

            // TODO: Is it better to use hidden fields and transformer for Member entity?
            $photo->setClass('none')->setEntity('member')->setMember($member);

            $form = $this->createForm('khatovar_photo_type', $photo);
            $form->remove('class')->remove('entity')->remove('member');

            $isEditor = false;
        } else {
            $form = $this->createForm('khatovar_photo_type', $photo);
            $isEditor = true;
        }

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->entityManager->persist($photo);
            $this->entityManager->flush();

            $this->photoManager->imageResize($photo->getAbsolutePath(), static::MAX_PHOTO_HEIGHT);

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Photo ajoutée'
            );

            if ($isEditor) {
                return $this->redirect(
                    $this->generateUrl(
                        'khatovar_web_photos_edit',
                        array('photo'=> $photo->getId())
                    )
                );
            }

            return $this->redirect($this->generateUrl('khatovar_web_photos'));
        }

        return $this->render(
            'KhatovarPhotoBundle:Photo:add.html.twig',
            array(
                'form'  => $form->createView(),
                'photo' => $photo,
            )
        );
    }

    /**
     * Edit a photo information.
     *
     * @param Photo   $photo The photo to edit.
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_VIEWER")
     */
    public function editAction(Photo $photo, Request $request)
    {
        $entity = $photo->getEntity();
        $member = $this->getLoggedMember();
        $form   = $this->createForm('khatovar_photo_type', $photo);

        if (!$this->isGranted('ROLE_EDITOR')) {
            if (!$member) {
                return $this->render(
                    'KhatovarPhotoBundle:Photo:add.html.twig',
                    array('not_a_member' => 1)
                );
            }
            $form->remove('class')->remove('entity')->remove('member');
        }

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->entityManager->persist($photo);

            if ($photo->getEntity() !== $entity) {
                $photo->setHomepage(null)->setMember(null);
                $this->entityManager->flush();

                return $this->redirect(
                    $this->generateUrl(
                        'khatovar_web_photos_edit',
                        array('photo'=> $photo->getId())
                    )
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Photo modifiée'
                );

                $this->entityManager->flush();
            }

            return $this->redirect($this->generateUrl('khatovar_web_photos'));
        }

        return $this->render(
            'KhatovarPhotoBundle:Photo:edit.html.twig',
            array(
                'photo' => $photo,
                'form'  => $form->createView(),
                'owner' => $member,
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
        $form   = $this->createFormBuilder()->getForm();
        $member = $this->getLoggedMember();

        if (!$this->isGranted('ROLE_EDITOR') and !$member) {
            return $this->render(
                'KhatovarPhotoBundle:Photo:delete.html.twig',
                array('not_an_editor' => 1)
            );
        }

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->entityManager->remove($photo);
            $this->entityManager->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Photo supprimée'
            );

            return $this->redirect($this->generateUrl('khatovar_web_photos'));
        }

        return $this->render(
            'KhatovarPhotoBundle:Photo:delete.html.twig',
            array(
                'photo' => $photo,
                'form'  => $form->createView(),
                'owner' => $member,
            )
        );
    }

    /**
     * Get the member page corresponding to the current user.
     *
     * @return \Khatovar\Bundle\MemberBundle\Entity\Member
     */
    protected function getLoggedMember()
    {
        return $this->entityManager
            ->getRepository('KhatovarMemberBundle:Member')
            ->findOneBy(array('owner' => $this->getUser()->getId()));
    }
}
