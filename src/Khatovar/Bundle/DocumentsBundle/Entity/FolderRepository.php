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

namespace Khatovar\Bundle\DocumentsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Folder repository.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FolderRepository extends EntityRepository
{
    /**
     * Returns all the folders that are at the root of the application,
     * ordered by name.
     *
     * @return Folder[]
     */
    public function findFoldersWithoutParentsOrderedByName()
    {
        return $this->createQueryBuilder('dir')
            ->where('dir.parent is NULL')
            ->orderBy('dir.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the parent of a folder, and its parent, and so on until root
     * folder is reached.
     *
     * @param string $id
     *
     * @return Folder[]
     */
    public function getParents($id)
    {
        $folder = $this->find($id);
        if (null === $folder) {
            [];
        }

        $parentFolders = [];
        $parentFolder = $folder->getParent();

        while (null !== $parentFolder) {
            $previousFolder = $parentFolder;
            $parentFolders[] = $previousFolder;
            $parentFolder = $previousFolder->getParent();
        }

        return array_reverse($parentFolders);
    }

    /**
     * Returns a folder with all the files it contains, ordered by name.
     *
     * @param string $id
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return null|Folder
     */
    public function findOneWithOrderedFiles($id)
    {
        $query = $this->createQueryBuilder('dir')
            ->leftJoin('dir.files', 'f')
                ->addSelect('f')
                ->orderBy('f.name')
            ->where('dir.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Return the children of a folder, ordered by name.
     *
     * @param string $id
     *
     * @return Folder[]
     */
    public function findChildrenOrderedByName($id)
    {
        $query = $this->createQueryBuilder('dir')
            ->where('dir.parent = :id')
            ->setParameter('id', $id)
            ->orderBy('dir.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
