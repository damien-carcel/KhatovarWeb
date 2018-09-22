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
 * File repository.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileRepository extends EntityRepository
{
    /**
     * Returns all the files that are at the root of the application,
     * ordered by name.
     *
     * @return File[]
     */
    public function findFilesWithoutParentsOrderedByName()
    {
        return $this->createQueryBuilder('file')
            ->where('file.folder is NULL')
            ->orderBy('file.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
