<?php

declare(strict_types=1);

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

use Khatovar\Bundle\WebBundle\Entity\ActivableEntity;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Handles the activation of an entity and deactivation of the previous active one.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ActivationHandler
{
    /** @var RegistryInterface */
    protected $doctrine;

    /** @var string */
    protected $entity;

    /**
     * @param RegistryInterface $doctrine
     * @param string            $entity
     */
    public function __construct(RegistryInterface $doctrine, $entity)
    {
        $this->doctrine = $doctrine;
        $this->entity = $entity;
    }

    /**
     * @param ActivableEntity $newActiveEntity
     */
    public function handle(ActivableEntity $newActiveEntity): void
    {
        $repository = $this->doctrine->getRepository($this->entity);
        $oldContact = $repository->findOneBy(['active' => true]);

        if (null !== $oldContact) {
            $oldContact->deactivate();
            $this->doctrine->getManager()->persist($oldContact);
        }

        $newActiveEntity->activate();
        $this->doctrine->getManager()->persist($newActiveEntity);

        $this->doctrine->getManager()->flush();
    }
}
