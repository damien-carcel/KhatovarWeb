<?php

declare(strict_types=1);

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

namespace Khatovar\Bundle\DocumentsBundle\Doctrine;

use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class EntityListenerResolver extends DefaultEntityListenerResolver
{
    /** @var ContainerInterface */
    protected $container;

    /** @var array */
    protected $mapping;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mapping = [];
    }

    /**
     * @param string $className
     * @param string $service
     */
    public function addMapping($className, $service): void
    {
        $this->mapping[$className] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($className)
    {
        if (isset($this->mapping[$className]) && $this->container->has($this->mapping[$className])) {
            return $this->container->get($this->mapping[$className]);
        }

        return parent::resolve($className);
    }
}
