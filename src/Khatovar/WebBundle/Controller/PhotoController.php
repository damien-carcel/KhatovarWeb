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

        // If an editor or more powerful user is connected, we return
        // all photos, but is it is a regular user, we only return its
        // own photos, as he cannot edit/delete others.
        if ($currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            $photos = $entityManager->getRepository('KhatovarWebBundle:Photo')
                ->getAllOrdered();
        } else {
            $entry = $entityManager->getRepository('KhatovarWebBundle:Member')
                ->findOneBy(array('owner' => $currentUser->getId()));
            $photos = $entityManager->getRepository('KhatovarWebBundle:Photo')
                ->findBy(
                    array(
                        'entity' => 'member',
                        'entry' => $entry
                    )
                );
        }

        $filter = $this->get('khatovar.filters.array');
        $entityList = array(
            'homepage' => array(
                'name' => 'Pages d’accueil'
            ),
            'member' => array(
                'name' => 'Membres',
                'list' => $filter->returnArray('member')
            )
        );

        return $this->render(
            'KhatovarWebBundle:Photo:index.html.twig',
            array(
                'photos' => $photos,
                'entity_list' => $entityList
            )
        );
    }

    /**
     * Display a list of all photos uploaded for the current page in a
     * small sidebar.
     *
     * @param string $entity Display only the photos attached to this
     * entity.
     * @param string|int $entry The slug or the ID of the object
     * currently rendered in the web page.
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function sideAction($entity, $entry)
    {
        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        // First we retrieve the ID of the object displayed on the web page.
        // If entry is a string, then it is an object slug
        if ($entity != 'default' and !is_null($entry) and is_string($entry)) {
            // TODO: Should be a way to do this without quering it again, as it is already rendered on the main page
            $currentRenderId = $entityManager
                ->getRepository('KhatovarWebBundle:' . ucfirst($entity))
                ->findOneBy(array('slug' => $entry))
                ->getId();
        // If it is an integer, then it is an object ID
        } elseif ($entity != 'default' and !is_null($entry) and is_int($entry)) {
            $currentRenderId = $entry;
        // If $entry is null, then there is no reason to display any photos
        } else {
            $currentRenderId = null;
        }

        // If current user is an editor, then he can access photos of all pages
        if ($currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            // If we render a homepage, then we retrieve all homepages photos
            if ($entity == 'homepage') {
                $photos = $entityManager
                    ->getRepository('KhatovarWebBundle:Photo')
                    ->findBy(array('entity' => $entity));
            // If not, we retrieve only the photo of the displayed page
            } elseif (!is_null($currentRenderId)) {
                $photos = $entityManager->getRepository('KhatovarWebBundle:Photo')
                    ->findBy(array(
                            'entity' => $entity,
                            'entry' => $currentRenderId
                        ));
            } else {
                $photos = '';
            }
        // But if current user is a regular one, he can only access his own
        // photos and only when viewing/editing its own member page.
        } else {
            $owned = $entityManager
                ->getRepository('KhatovarWebBundle:Member')
                ->findOneBy(array('owner' => $currentUser))
                ->getId();

            if (!is_null($currentRenderId) and $owned == $currentRenderId) {
                $photos = $entityManager->getRepository('KhatovarWebBundle:Photo')
                    ->findBy(array(
                            'entity' => $entity,
                            'entry' => $owned
                        ));
            } else {
                $photos = '';
            }
        }

        return $this->render(
            'KhatovarWebBundle:Photo:side.html.twig',
            array('photos' => $photos)
        );
    }

    /**
     * Add a new photo to the collection.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function addAction()
    {
        $photo = new Photo();
        $form = $this->createForm(new PhotoType(array()), $photo);

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();
        $entry = $this->getDoctrine()->getManager()
            ->getRepository('KhatovarWebBundle:Member')
            ->findOneBy(array('owner' => $currentUser->getId()));

        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            // If the user doesn't have a member's page, then he have no
            // reason to add photos
            if (!$entry) {
                return $this->render(
                    'KhatovarWebBundle:Photo:add.html.twig',
                    array(
                        'not_a_member' => 1
                    )
                );
            }
            // If he has one, then he can upload, but only for its own
            // member's page.
            $form->remove('entity')->remove('entry');
            $form->add('entity', 'hidden', array(
                    'data' => 'member'
                ))
                ->add('entry', 'hidden', array(
                        'data' => $entry
                    ));
        }

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
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

                return $this->redirect(
                    $this->generateUrl(
                        'khatovar_web_photos_edit',
                        array('photo'=> $photo->getId())
                    )
                );
            }
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function editAction(Photo $photo)
    {
        // We get all entries corresponding to the current photo entity
        $choices = $this->getDoctrine()
            ->getRepository('KhatovarWebBundle:' . ucfirst($photo->getEntity()))
            ->findAll();

        // Then keep them as an id=>name array
        $list = array();
        foreach ($choices as $choice) {
            /**
             * @var $choice Homepage
             * Only for editor autocompletion.
             * In reality, it can be any entity except Photo.
             */
            $list[$choice->getId()] = $choice->getName();
        }
        asort($list);

        // And inject this array in the form tye to use it as the entry choice
        $form = $this->createForm(new PhotoType($list), $photo);
        $entity = $photo->getEntity();

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();
        $entry = $this->getDoctrine()->getManager()
            ->getRepository('KhatovarWebBundle:Member')
            ->findOneBy(array('owner' => $currentUser->getId()));

        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR')) {
            // If the user doesn't have a member's page, then he have no
            // reason to edit photos
            if (!$entry) {
                return $this->render(
                    'KhatovarWebBundle:Photo:add.html.twig',
                    array(
                        'not_a_member' => 1
                    )
                );
            }
            // If he has one, then he can edit, but only for its own
            // member's page.
            $form->remove('entity')->remove('entry');
            $form->add('entity', 'hidden', array(
                    'data' => 'member'
                ))
                ->add('entry', 'hidden', array(
                        'data' => $entry
                    ));
        }

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($photo);
                $entityManager->flush();

                // We check if the entity was changed, because if it
                // was, then the other attributes (class or entry) may
                // have to be changed too.
                if ($photo->getEntity() != $entity) {
                    return $this->redirect(
                        $this->generateUrl(
                            'khatovar_web_photos_edit',
                            array('photo'=> $photo->getId())
                        )
                    );
                } else {
                    $this->get('session')->getFlashBag()
                        ->add('notice', 'Photo modifiée');
                }

                return $this->redirect(
                    $this->generateUrl('khatovar_web_photos')
                );
            }
        }

        return $this->render(
            'KhatovarWebBundle:Photo:edit.html.twig',
            array(
                'photo' => $photo,
                'form' => $form->createView(),
                'owner' => $entry
            )
        );
    }

    /**
     * Remove a photo from the collection.
     *
     * @param Photo $photo The photo to delete.
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_VIEWER")
     */
    public function deleteAction(Photo $photo)
    {
        // As it is only to delete the photo, we just need an empty form
        $form = $this->createFormBuilder()->getForm();
        $request = $this->get('request');

        $currentUser = $this->container->get('security.context')
            ->getToken()->getUser();
        $entry = $this->getDoctrine()->getManager()
            ->getRepository('KhatovarWebBundle:Member')
            ->findOneBy(array('owner' => $currentUser->getId()));

        if (!$currentUser->hasRole('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_EDITOR') and !$entry) {
            // If the user doesn't have a member's page, then he have no
            // reason to delete photos
            return $this->render(
                'KhatovarWebBundle:Photo:add.html.twig',
                array(
                    'not_a_member' => 1
                )
            );
        }

        if ($request->getMethod() == 'POST') {
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
        }

        return $this->render(
            'KhatovarWebBundle:Photo:delete.html.twig',
            array(
                'photo' => $photo,
                'form' => $form->createView(),
                'owner' => $entry
            )
        );
    }
}
