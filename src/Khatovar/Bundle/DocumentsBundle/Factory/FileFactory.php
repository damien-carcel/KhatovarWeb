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

namespace Khatovar\Bundle\DocumentsBundle\Factory;

use Doctrine\Common\Persistence\ObjectRepository;
use Khatovar\Bundle\DocumentsBundle\Entity\File;

/**
 * Creates a new instance of File, with parent folder and upload
 * directory already set.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileFactory
{
    /** @var ObjectRepository */
    protected $repository;

    /**
     * The path to upload the file.
     *
     * @var string
     */
    protected $uploadDirectory;

    /**.
     * FileFactory constructor.
     *
     * @param ObjectRepository $repository
     * @param string           $uploadDirectory
     */
    public function __construct(ObjectRepository $repository, $uploadDirectory)
    {
        $this->repository = $repository;

        if ('/' === substr($uploadDirectory, -1)) {
            $uploadDirectory = substr($uploadDirectory, 0, -1);
        }
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @param int $folderId
     *
     * @return File
     */
    public function createFile($folderId)
    {
        $file = new File();
        $file->setUploadDir($this->uploadDirectory);
        if (null !== $folder = $this->repository->find($folderId)) {
            $file->setFolder($folder);
        }

        return $file;
    }
}
