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

namespace Khatovar\Bundle\DocumentsBundle\Entity\Listener;

use Khatovar\Bundle\DocumentsBundle\Entity\File;

/**
 * This listener allow to pass the "khatovar_document.upload_dir" parameter to the File entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileListener
{
    /** @var string */
    protected $uploadDirectory;

    /**
     * @param string $uploadDirectory
     */
    public function __construct($uploadDirectory)
    {
        if ('/' === substr($uploadDirectory, -1)) {
            $uploadDirectory = substr($uploadDirectory, 0, -1);
        }

        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @param File $file
     */
    public function postLoad(File $file)
    {
        $file->setUploadDir($this->uploadDirectory);
    }
}
