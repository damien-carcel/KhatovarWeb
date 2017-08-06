<?php

/**
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2015 Damien Carcel (https://github.com/damien-carcel)
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
 */

namespace Khatovar\Bundle\PhotoBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\PhotoBundle\Entity\Photo;
use Khatovar\Bundle\PhotoBundle\Helper\PhotoHelper;
use Khatovar\Bundle\PhotoBundle\Manager\PhotoManager;
use Symfony\Component\Routing\RouterInterface;

/**
 * Handles Photo entity creation and updates.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class PhotoHandler
{
    /** @staticvar string */
    const MAX_PHOTO_HEIGHT = 720;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var PhotoManager */
    protected $photoManager;

    /** @var RouterInterface */
    protected $router;

    /**
     * @param EntityManagerInterface $entityManager
     * @param PhotoManager           $photoManager
     * @param RouterInterface        $router
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PhotoManager $photoManager,
        RouterInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->photoManager = $photoManager;
        $this->router = $router;
    }

    /**
     * Saves a new Photo entity and resizes the corresponding image file.
     *
     * @param Photo $photo
     */
    public function handleCreation(Photo $photo)
    {
        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        $this->photoManager->imageResize(
            $photo->getAbsolutePath(),
            static::MAX_PHOTO_HEIGHT
        );
    }

    /**
     * Saves a edited photo and returns the route to redirect to.
     *
     * If the attached entity has been changed, it returns the route to
     * edit the photo again, allowing to change the attached page. If
     * not, it returns to the photo main page.
     *
     * @param Photo  $photo
     * @param string $entity
     *
     * @return string
     */
    public function handleUpdate(Photo $photo, $entity)
    {
        $this->entityManager->persist($photo);

        if ($photo->getEntity() !== $entity) {
            foreach (PhotoHelper::getPhotoEntities() as $code) {
                $setter = 'set'.ucfirst($code);
                $photo->$setter(null);
            }

            $route = $this->router->generate(
                'khatovar_web_photo_edit',
                ['id' => $photo->getId()]
            );
        } else {
            $route = $this->router->generate('khatovar_web_photo');
        }

        $this->entityManager->flush();

        return $route;
    }
}
