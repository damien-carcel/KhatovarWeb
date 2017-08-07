<?php

/**
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

namespace Khatovar\Bundle\WebBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class Homepage
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $content;

    /** @var bool */
    protected $active;

    /** @var \Doctrine\Common\Collections\Collection $photos */
    protected $photos;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->active = false;
    }

    /**
     * Allow to save only the entity's ID in database as a string when
     * using entity form type.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) ($this->id);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Homepage
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Homepage
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Add photos.
     *
     * @param Photo $photos
     *
     * @return Homepage
     */
    public function addPhoto(Photo $photos)
    {
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos.
     *
     * @param Photo $photos
     *
     * @return Homepage
     */
    public function removePhoto(Photo $photos)
    {
        $this->photos->removeElement($photos);

        return $this;
    }

    /**
     * Get photos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Homepage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
