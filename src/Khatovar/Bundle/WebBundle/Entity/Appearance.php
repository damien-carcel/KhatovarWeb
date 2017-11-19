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
class Appearance implements ActivableEntity
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $slug;

    /** @var string */
    protected $content;

    /** @var bool */
    protected $active;

    /** @var string */
    protected $pageType;

    /** @var \Doctrine\Common\Collections\Collection */
    protected $photos;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->active = false;
        $this->photos = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) ($this->id);
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
     * @return Appearance
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
     * @param string $slug
     *
     * @return Appearance
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $content
     *
     * @return Appearance
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
     * {@inheritdoc}
     */
    public function activate()
    {
        $this->active = true;
    }

    /**
     * {@inheritdoc}
     */
    public function deactivate()
    {
        $this->active = false;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param string $pageType
     *
     * @return Appearance
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * Add photos.
     *
     * @param Photo $photos
     *
     * @return Appearance
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
     * @return Appearance
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
}
