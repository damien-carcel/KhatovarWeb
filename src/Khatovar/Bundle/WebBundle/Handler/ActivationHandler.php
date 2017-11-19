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

namespace Khatovar\Bundle\WebBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\WebBundle\Entity\ActivableEntity;

/**
 * Handles the activation of an entity and deactivation of the previous active one.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ActivationHandler
{
    /** @var string */
    protected $entity;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entity
     */
    public function __construct(EntityManagerInterface $entityManager, $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
    }

    /**
     * @param ActivableEntity $newActiveEntity
     */
    public function handle(ActivableEntity $newActiveEntity)
    {
        $repository = $this->entityManager->getRepository($this->entity);
        $oldContact = $repository->findOneBy(['active' => true]);

        if (null !== $oldContact) {
            $oldContact->deactivate();
            $this->entityManager->persist($oldContact);
        }

        $newActiveEntity->activate();
        $this->entityManager->persist($newActiveEntity);

        $this->entityManager->flush();
    }
}
