<?php
/**
 *
 * This file is part of Documents.
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
 *
 * @copyright   Copyright (C) Damien Carcel (https://github.com/damien-carcel)
 * @link        https://github.com/damien-carcel/Documents
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Carcel\DocumentsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * File
 *
 * @ORM\Table(name="carcel_documents_files")
 * @ORM\Entity(repositoryClass="Carcel\DocumentsBundle\Entity\FileRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class File
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(min="3", max="255")
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_name", type="integer")
     */
    private $fileName;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="60000000")
     */
    private $filePath;

    /**
     * @var string
     */
    private $tempFile;

    /**
     * @ORM\ManyToOne(targetEntity="Carcel\DocumentsBundle\Entity\Folder", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $folder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Assert\DateTime()
     *
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     * @Assert\DateTime()
     */
    private $modified;


    /**
     * Create an instance of Documents, and initialize the dates of
     * modification and creation if they are not set.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
    }

    /**
     * Get the file on the server.
     *
     * @return UploadedFile
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set the path to the file.
     *
     * @param UploadedFile $filePath
     */
    public function setFilePath(UploadedFile $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Set the attributes for the file to update/persist.
     *
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function preUpload()
    {
        if (is_null($this->filePath)) {
            return;
        }

        $this->name = $this->filePath->getClientOriginalName();
        $this->fileName = time();
    }

    /**
     * Upload the file on the server.
     * If a file with the same name already exists, it will be replaced.
     *
     * @ORM\PostUpdate()
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if (is_null($this->filePath)) {
            return;
        }

        $this->filePath->move($this->getDir(), $this->fileName);
    }

    /**
     * We save the real file name on the server before its database
     * entry is removed.
     *
     * @ORM\PreRemove
     */
    public function preRemoveUpload()
    {
        $this->tempFile = $this->getDir() . '/' . $this->getFileName();
    }

    /**
     * Remove the file from the server, and also remove folders if they
     * are empty.
     *
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
            $this->removeDir($this->tempFile);
        }
    }

    /**
     * If empty, remove the folders that contained a file that have
     * been deleted.
     *
     * @param string $file The path to the file that have been deleted.
     */
    protected function removeDir($file)
    {
        $path = substr($file, 0, -strlen(strrchr($file, '/')));
        $content = array_diff(scandir($path), array('..', '.'));
        //Erase DAY folder if empty
        if (scandir($path) == true and empty($content)) {
            rmdir($path);
            $path = substr($path, 0, -strlen(strrchr($path, '/')));
            $content = array_diff(scandir($path), array('..', '.'));
            // Erase MONTH folder if empty
            if (scandir($path) == true and empty($content)) {
                rmdir($path);
                $path = substr($path, 0, -strlen(strrchr($path, '/')));
                $content = array_diff(scandir($path), array('..', '.'));
                // Erase YEAR folder if empty
                if (scandir($path) == true and empty($content)) {
                    rmdir($path);
                }
            }
        }
    }

    /**
     * Get the absolute path to the directory that contain the file.
     *
     * @return string
     */
    protected function getDir()
    {
        return  __DIR__ . '/../../../../www/uploaded/' .
            date('Y', $this->fileName) . '/' . date('m', $this->fileName)
            . '/' . date('d', $this->fileName);
    }

    /**
     * Get the relative path to the directory that contain the file.
     *
     * @return string
     */
    public function getWebDir()
    {
        return 'uploaded/' . date('Y', $this->fileName) . '/'
            . date('m', $this->fileName) . '/' . date('d', $this->fileName);
    }

    /**
     * Return the absolute path to the file.
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->getDir()  . '/' .  $this->fileName;
    }

    /**
     * Return mime type of the file.
     *
     * @return mixed
     */
    public function getMime()
    {
        $guesser = MimeTypeGuesser::getInstance();
        return $guesser->guess($this->getPath());
    }

    /**
     * Return a human readable value of the size of the file.
     *
     * @return string
     */
    public function getSize()
    {
        $base = 1024;
        $size = filesize($this->getPath());

        $units = explode(' ', 'octets ko Mo Go To Po');
        for ($i = 0; $size > $base; $i++) {
            $size /= $base;
        }

        return round($size, 1) . ' ' . $units[$i];
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateModified()
    {
        $this->setModified(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return File
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return File
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set fileName
     *
     * @param integer $fileName
     * @return File
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return integer
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set folder
     *
     * @param \Carcel\DocumentsBundle\Entity\Folder $folder
     * @return File
     */
    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return \Carcel\DocumentsBundle\Entity\Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
