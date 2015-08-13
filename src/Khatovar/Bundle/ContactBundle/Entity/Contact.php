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

namespace Khatovar\Bundle\ContactBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Khatovar\Bundle\PhotoBundle\Entity\Photo;

/**
 * Contact entity.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class Contact
{
    /** @var integer */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $content;

    /** @var bool */
    protected $active;

    /** @var Photo */
    protected $visitCard;

    /** @var ArrayCollection */
    protected $photos;

    /**
     * Allow to save only the entity's ID in database as a string when
     * using entity form type.
     *
     * @return string
     */
    public function __toString()
    {
        return strval($this->id);
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->active = false;
        $this->photos = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Contact
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
     * @param string $content
     *
     * @return Contact
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return Contact
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Photo
     */
    public function getVisitCard()
    {
        return $this->visitCard;
    }

    /**
     * @param Photo $visitCard
     *
     * @return Contact
     */
    public function setVisitCard(Photo $visitCard = null)
    {
        $this->visitCard = $visitCard;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Add photo.
     *
     * @param Photo $photo
     *
     * @return Contact
     */
    public function addPhoto(Photo $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo.
     *
     * @param Photo $photo
     *
     * @return Contact
     */
    public function removePhoto(Photo $photo)
    {
        $this->photos->removeElement($photo);

        return $this;
    }
}
