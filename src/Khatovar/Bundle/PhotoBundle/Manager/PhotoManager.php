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

namespace Khatovar\Bundle\PhotoBundle\Manager;

use Carcel\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\PhotoBundle\Entity\Photo;
use Khatovar\Bundle\PhotoBundle\Helper\PhotoHelper;
use Khatovar\Bundle\WebBundle\Helper\EntityHelper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Photo manager.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class PhotoManager
{
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /**
     * @param EntityManagerInterface        $entityManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager        = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage         = $tokenStorage;
    }

    /**
     * Look for special photo insertion tags and transform it in html syntax.
     *
     * @param string $text The text to transform.
     * @return string
     */
    public function imageTranslate($text)
    {
        $text  = $this->insertTagsInText($text);
        $paths = $this->getPhotoPaths($text);

        $repository = $this->entityManager->getRepository('KhatovarPhotoBundle:Photo');

        $photos = [];

        foreach ($paths as $path) {
            $photo = $repository->findOneBy(['path' => $path]);
            if (null !== $photo) {
                $photos[] = '<a href="/uploaded/photos/'
                    . $photo->getPath()
                    . '" data-lightbox="Photos Khatovar" title="Copyright &copy; '
                    . date('Y')
                    . ' association La Compagnie franche du Khatovar"><img class="'
                    . $photo->getClass()
                    . ' photo_rest" onmouseover="this.className=\''
                    . $photo->getClass()
                    . ' photo_over\'" onmouseout="this.className=\''
                    . $photo->getClass()
                    . ' photo_rest\'" src="/uploaded/photos/'
                    . $photo->getPath()
                    . '" alt="' . $photo->getAlt()
                    . '" /></a>';
            } else {
                $photos[] = 'Cette photo n\'existe pas';
            }
        }

        return str_replace($paths, $photos, $text);
    }

    /**
     * Resize an jpeg image according to a given height, but only if
     * the original image is higher.
     *
     * @param string $image The path to the original image
     * @param int $newHeight
     */
    public function imageResize($image, $newHeight)
    {
        // We first find the dimensions of the photo and its ratio
        $original = imagecreatefromjpeg($image);
        list($width, $height) = getimagesize($image);
        $ratio = $width / $height;

        if ($height > $newHeight) {
            $newWidth = round($newHeight * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $original, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            copy($image, $image . '.old');
            unlink($image);

            if (imagejpeg($resized, $image)) {
                unlink($image . '.old');
            } else {
                copy($image . '.old', $image);
            }

            imagedestroy($resized);
        }

        imagedestroy($original);
    }

    /**
     * Get a sorted list of all photos currently uploaded.
     *
     * @return array
     */
    public function getPhotosSortedByEntities()
    {
        $sortedPhotos = [
            'Photos orphelines' => [
                'Liste des photos n\'appartenant à aucune page' => $this->entityManager
                    ->getRepository('KhatovarPhotoBundle:Photo')
                    ->getOrphans(),
            ],
        ];

        foreach (PhotoHelper::getPhotoEntities() as $label => $code) {
            $sortedPhotos[$label] = $this->getEntityPhotos($code);
        }

        return $sortedPhotos;
    }

    /**
     * Get a member's photos.
     *
     * @param User $currentUser
     *
     * @return Photo[]
     */
    public function getMemberPhotos(User $currentUser)
    {
        $sortedPhotos = [];

        $member = $this->entityManager
            ->getRepository('KhatovarMemberBundle:Member')
            ->findOneBy(['owner' => $currentUser]);

        $memberPhotos = $member->getPhotos();

        foreach ($memberPhotos as $photo) {
            $sortedPhotos['Membre'][$member->getName()][] = $photo;
        }

        return $sortedPhotos;
    }

    /**
     * Return the list of all photos of the current page that the user
     * can access.
     *
     * @param string $controller
     * @param string $action
     * @param string $slugOrId
     *
     * @return \Khatovar\Bundle\PhotoBundle\Entity\Photo[]
     */
    public function getControllerPhotos($controller, $action, $slugOrId)
    {
        $photos = [];

        if ('web' === $controller) {
            return $photos;
        }

        $currentlyRendered = $this->getCurrentlyRendered($controller, $action, $slugOrId);

        $owner = null;
        if (EntityHelper::MEMBER_CODE === $controller && null != $slugOrId) {
            $owner = $currentlyRendered->getOwner();
        }

        if (null !== $currentlyRendered) {
            if ($this->authorizationChecker->isGranted('ROLE_EDITOR') ||
                ($owner === $this->tokenStorage->getToken()->getUser())
            ) {
                $photos = $currentlyRendered->getPhotos();
            }
        }

        return $photos;
    }

    /**
     * Return the entity which content is currently rendered by the
     * application.
     *
     * @param $controller
     * @param $action
     * @param $slugOrId
     *
     * @return null|object
     */
    protected function getCurrentlyRendered($controller, $action, $slugOrId)
    {
        $currentlyRendered = null;
        $repo = $this->getRepository($controller);

        if (null === $repo || $controller === 'photo') {
            return null;
        }

        if (in_array($controller, EntityHelper::getActivables()) && null == $slugOrId && $action === 'index') {
            $currentlyRendered = $repo->findOneBy(['active' => true]);
        } elseif (null != $slugOrId) {
            if (is_string($slugOrId)) {
                $currentlyRendered = $repo->findOneBy(['slug' => $slugOrId]);
            } elseif (is_int($slugOrId)) {
                $currentlyRendered = $repo->find($slugOrId);
            }
        }

        return $currentlyRendered;
    }

    /**
     * Get entity repository for a corresponding controller.
     *
     * @param string $controller
     *
     * @return \Doctrine\ORM\EntityRepository|null
     */
    protected function getRepository($controller)
    {
        $entity = 'Khatovar' . ucfirst($controller) . 'Bundle:' . ucfirst($controller);

        return $this->entityManager->getRepository($entity);
    }

    /**
     * @param string $text
     *
     * @return array
     */
    protected function getPhotoPaths($text)
    {
        preg_match_all('#(\w+\-\d+\.jpeg)#', $text, $matches);

        return $matches[0];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function insertTagsInText($text)
    {
        $text = str_replace('<p>[', '<div>[', $text);
        $text = str_replace(']</p>', ']</div>', $text);

        $text = str_replace('<div>[', '<div class="container">', $text);
        $text = str_replace(']</div>', '</div>', $text);
        $text = str_replace('][', '', $text);

        return $text;
    }

    /**
     * Get all photos owned by a given entity type.
     *
     * @param string $entityCode
     *
     * @return Photo[]
     */
    protected function getEntityPhotos($entityCode)
    {
        $sortedPhotos = [];

        $entities = $this->entityManager
            ->getRepository('Khatovar' . ucfirst($entityCode) . 'Bundle:' . ucfirst($entityCode))
            ->findAll();

        foreach ($entities as $entity) {
            $photos = $entity->getPhotos();
            foreach ($photos as $photo) {
                if (EntityHelper::EXACTION_CODE === $entityCode) {
                    $name = $entity->getCompleteName();
                } else {
                    $name = $entity->getName();
                }

                $sortedPhotos[$name][] = $photo;
            }
        }

        return $sortedPhotos;
    }
}
