<?php

/*
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

namespace Khatovar\Bundle\DocumentsBundle\Saver;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Base entity saver.
 * This must be declared as different services for different classes.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class BaseSaver implements SaverInterface
{
    /** @var RegistryInterface */
    protected $doctrine;

    /** @var SavingOptionsResolverInterface */
    protected $optionsResolver;

    /** @var string */
    protected $entityClass;

    /**
     * @param RegistryInterface              $doctrine
     * @param SavingOptionsResolverInterface $optionsResolver
     * @param string                         $entityClass
     */
    public function __construct(
        RegistryInterface $doctrine,
        SavingOptionsResolverInterface $optionsResolver,
        $entityClass
    ) {
        $this->doctrine = $doctrine;
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

        $this->doctrine->getManager()->persist($object);

        if (true === $options['flush']) {
            $this->doctrine->getManager()->flush();
        }
    }
}
