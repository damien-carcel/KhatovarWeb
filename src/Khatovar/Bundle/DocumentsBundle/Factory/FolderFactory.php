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

namespace Khatovar\Bundle\DocumentsBundle\Factory;

use Doctrine\Common\Persistence\ObjectRepository;
use Khatovar\Bundle\DocumentsBundle\Entity\Folder;

/**
 * Creates a new instance of Folder, with parent folder set.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FolderFactory
{
    /** @var ObjectRepository */
    protected $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $parentId
     *
     * @return Folder
     */
    public function createFolder($parentId)
    {
        $folder = new Folder();
        if (null !== $parent = $this->repository->find($parentId)) {
            $folder->setParent($parent);
        }

        return $folder;
    }
}
