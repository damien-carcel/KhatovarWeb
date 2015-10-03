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

use Khatovar\Bundle\AppearanceBundle\Entity\Appearance;
use Khatovar\Bundle\ContactBundle\Entity\Contact;
use Khatovar\Bundle\ExactionBundle\Entity\Exaction;
use Khatovar\Bundle\HomepageBundle\Entity\Homepage;
use Khatovar\Bundle\MemberBundle\Entity\Member;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Photo entity.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class Photo
{
    /** @var integer */
    protected $id;

    /**
     * The alternate text to display.
     *
     * @var string
     */
    protected $alt;

    /**
     * The CSS class used for resizing.
     *
     * @var string
     */
    protected $class;

    /**
     * The location of the file on the server.
     *
     * @var string
     */
    protected $path;

    /**
     * Temporary attribute to remember the file path when deleting it.
     *
     * @var string
     */
    protected $temp;

    /** @var UploadedFile */
    protected $file;

    /**
     * The type of page the photo is attached to.
     *
     * @var string
     */
    protected $entity;

    /** @var Homepage */
    protected $homepage;

    /** @var Member */
    protected $member;

    /** @var Exaction */
    protected $exaction;

    /** @var Contact */
    protected $contact;

    /** @var Appearance */
    protected $appearance;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->alt;
    }

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

        if ($this->path) {
            $this->temp = $this->path;
            $this->path = null;
        }
    }

    /**
     * Set the name of the file before uploading it.
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->path = 'photo-' . time() . '.'
                . $this->getFile()->guessExtension();
        }
    }

    /**
     * Upload the file on the server.
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $this->file->move($this->getUploadRootDir(), $this->path);

        if (isset($this->temp)) {
            unlink($this->getUploadRootDir().'/'.$this->temp);
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * Remove the file from the server.
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
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    /**
     * Return a relative path to the file.
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->path ? null : '/' . $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $alt
     *
     * @return Photo
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $class
     *
     * @return Photo
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $path
     *
     * @return Photo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $entity
     *
     * @return Photo
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Homepage $homepage
     *
     * @return Photo
     */
    public function setHomepage(Homepage $homepage = null)
    {
        $this->homepage = $homepage;

        return $this;
    }

    /**
     * @return Homepage
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * @param Member $member
     *
     * @return Photo
     */
    public function setMember(Member $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Exaction $exaction
     *
     * @return Photo
     */
    public function setExaction(Exaction $exaction = null)
    {
        $this->exaction = $exaction;

        return $this;
    }

    /**
     * @return Exaction
     */
    public function getExaction()
    {
        return $this->exaction;
    }

    /**
     * @return Photo
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     *
     * @return Photo
     */
    public function setContact(Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Appearance
     */
    public function getAppearance()
    {
        return $this->appearance;
    }

    /**
     * @param Appearance $appearance
     *
     * @return Photo
     */
    public function setAppearance(Appearance $appearance = null)
    {
        $this->appearance = $appearance;

        return $this;
    }

    /**
     * Return the absolute path of the directory containing the file.
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../../www/' . $this->getUploadDir();
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
}
