<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2015 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Bundle\DocumentsBundle\Saver;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Base entity saver.
 * This must be declared as different services for different classes.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class BaseSaver implements SaverInterface
{
    /** @var string */
    protected $entityClass;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var SavingOptionsResolverInterface */
    protected $optionsResolver;

    /**
     * @param EntityManagerInterface         $entityManager   the entity Manager
     * @param SavingOptionsResolverInterface $optionsResolver the option resolver
     * @param string                         $entityClass     the namespace of the entity class
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SavingOptionsResolverInterface $optionsResolver,
        $entityClass
    ) {
        $this->entityManager = $entityManager;
        $this->optionsResolver = $optionsResolver;
        $this->entityClass = $entityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, array $options = [])
    {
        if (!$object instanceof $this->entityClass) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expects a "%s", "%s" provided.',
                    $this->entityClass,
                    ClassUtils::getClass($object)
                )
            );
        }

        $options = $this->optionsResolver->resolveSaveOptions($options);

        $this->entityManager->persist($object);

        if (true === $options['flush']) {
            $this->entityManager->flush();
        }
    }
}
