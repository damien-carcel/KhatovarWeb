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

namespace Khatovar\Bundle\DocumentsBundle\Entity;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * File entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class File
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $fileName;

    /** @var UploadedFile */
    protected $filePath;

    /** @var string */
    protected $tempFile;

    /** @var Folder */
    protected $folder;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    /** @var string */
    protected $uploadDir;

    /**
     * Create an instance of Documents, and initialize the dates of
     * modification and creation if they are not set.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @param string $uploadDir
     *
     * @return File
     */
    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param UploadedFile $filePath
     *
     * @return File
     */
    public function setFilePath(UploadedFile $filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Set the attributes for the file to update/persist.
     */
    public function preUpload()
    {
        if (null === $this->filePath) {
            return;
        }

        $this->name = $this->filePath->getClientOriginalName();
        $this->fileName = sprintf('%s.%s', uniqid(), $this->filePath->getClientOriginalExtension());
    }

    /**
     * Upload the file on the server.
     * If a file with the same name already exists, it will be replaced.
     */
    public function upload()
    {
        if (null === $this->filePath) {
            return;
        }

        $this->filePath->move($this->getAbsoluteDir(), $this->fileName);
    }

    /**
     * We save the real file name on the server before its database
     * entry is removed.
     */
    public function preRemoveUpload()
    {
        $this->tempFile = $this->getAbsoluteDir().'/'.$this->getFileName();
    }

    /**
     * Remove the file from the server, and also remove folders if they
     * are empty.
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
            $this->removeDir($this->tempFile);
        }
    }

    /**
     * Return the absolute path to the file.
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->getAbsoluteDir().'/'.$this->fileName;
    }

    /**
     * Return mime type of the file.
     *
     * @return string
     */
    public function getMime()
    {
        $guesser = MimeTypeGuesser::getInstance();

        return $guesser->guess($this->getAbsolutePath());
    }

    /**
     * Return a human readable value of the size of the file.
     *
     * @return string
     */
    public function getSize()
    {
        $base = 1024;
        $size = filesize($this->getAbsolutePath());

        $units = explode(' ', 'octets ko Mo Go To Po');
        for ($i = 0; $size > $base; ++$i) {
            $size /= $base;
        }

        return round($size, 1).' '.$units[$i];
    }

    /**
     * Automatically set a new update value after a folder modification.
     */
    public function autoUpdate()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $created
     *
     * @return File
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $updated
     *
     * @return File
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param string $fileName
     *
     * @return File
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param Folder $folder
     *
     * @return File
     */
    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder.
     *
     * @return Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * If empty, remove the folders that contained a file that have
     * been deleted.
     *
     * @param string $file the path to the file that have been deleted
     */
    private function removeDir($file)
    {
        $path = substr($file, 0, -strlen(strrchr($file, '/')));
        $content = array_diff(scandir($path), ['..', '.']);
        //Erase DAY folder if empty
        if (true === scandir($path) && empty($content)) {
            rmdir($path);
            $path = substr($path, 0, -strlen(strrchr($path, '/')));
            $content = array_diff(scandir($path), ['..', '.']);
            // Erase MONTH folder if empty
            if (true === scandir($path) && empty($content)) {
                rmdir($path);
                $path = substr($path, 0, -strlen(strrchr($path, '/')));
                $content = array_diff(scandir($path), ['..', '.']);
                // Erase YEAR folder if empty
                if (true === scandir($path) && empty($content)) {
                    rmdir($path);
                }
            }
        }
    }

    /**
     * Gets the absolute path of the directory that contains the file.
     *
     * @return string
     */
    private function getAbsoluteDir()
    {
        return $this->uploadDir.'/'.$this->getRelativeDir();
    }

    /**
     * Gets the relative (to the configured upload directory) path of the
     * directory that contain the file.
     *
     * @return string
     */
    private function getRelativeDir()
    {
        return  $this->created->format('Y').'/'.$this->created->format('m').'/'.$this->created->format('d');
    }
}
