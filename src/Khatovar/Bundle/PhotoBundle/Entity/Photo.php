<?php
/**
 *
 * This file is part of KhatovarWeb.
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
 * @link        https://github.com/damien-carcel/KhatovarWeb
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\PhotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Photo
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\PhotoBundle\Entity
 *
 * @ORM\Table(name="khatovar_web_photos")
 * @ORM\Entity(repositoryClass="Khatovar\Bundle\PhotoBundle\Entity\PhotoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Photo
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
     * The alternate text to display.
     *
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     */
    private $alt;

    /**
     * The CSS class used for resizing.
     *
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $class;

    /**
     * The location of the file on the server.
     *
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * Temporary attribute to remember the file path when deleting it.
     *
     * @var string
     */
    private $temp;

    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="8000000", mimeTypes={"image/jpeg"})
     */
    private $file;

    /**
     * The type of page the photo is attached to.
     *
     * @var string
     *
     * @ORM\Column(name="entity", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $entity;

    /**
     * @ORM\ManyToOne(targetEntity="Khatovar\WebBundle\Entity\Homepage", cascade={"detach"}, inversedBy="photos")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $homepage;

    /**
     * @ORM\ManyToOne(targetEntity="Khatovar\WebBundle\Entity\Member", cascade={"detach"}, inversedBy="photos")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $member;


    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        // If there already is a path, it is stored in the $temp
        // attribute in case of a future deletion
        if ($this->path) {
            $this->temp = $this->path;
            $this->path = null;
        }
    }

    /**
     * Set the name of the file before uploading it.
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (!is_null($this->file)) {
            // The photo is named according to the time stamp of the upload.
            $this->path = 'photo-' . time() . '.'
                . $this->getFile()->guessExtension();
        }
    }

    /**
     * Upload the file on the server.
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (is_null($this->file)) {
            return;
        }

        $this->file->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image, and then delete it
        if (isset($this->temp)) {
            unlink($this->getUploadRootDir().'/'.$this->temp);
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * Remove the file from the server.
     *
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }

    /**
     * Return the absolute path to the file.
     *
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return is_null($this->path)
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    /**
     * Return a relative path to the file.
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return is_null($this->path)
            ? null
            : '/' . $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * Return the absolute path of the directory containing the file.
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../www/' . $this->getUploadDir();
    }

    /**
     * Return the relative path of the directory containing the file.
     * Useful for linking the file inside html page.
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploaded/photos';
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
     * Set alt
     *
     * @param string $alt
     * @return Photo
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set size
     *
     * @param string $class
     * @return Photo
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Photo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set homepage
     *
     * @param \Khatovar\WebBundle\Entity\Homepage $homepage
     * @return Photo
     */
    public function setHomepage(Homepage $homepage = null)
    {
        $this->homepage = $homepage;

        return $this;
    }

    /**
     * Get homepage
     *
     * @return \Khatovar\WebBundle\Entity\Homepage
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set member
     *
     * @param \Khatovar\WebBundle\Entity\Member $member
     * @return Photo
     */
    public function setMember(Member $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \Khatovar\WebBundle\Entity\Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return Photo
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
