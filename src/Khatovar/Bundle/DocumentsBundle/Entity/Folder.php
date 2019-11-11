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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Folder entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class Folder
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var Folder */
    protected $parent;

    /** @var Collection */
    protected $children;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    /** @var Collection */
    protected $files;

    /**
     * Create an instance of Folder, and initialize the dates of
     * modification and creation if they are not set.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->files = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Automatically set a new update value after a folder modification.
     */
    public function autoUpdate(): void
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
     */
    public function addFile(File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * @return Folder
     */
    public function removeFile(File $files)
    {
        $this->files->removeElement($files);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param Folder $children
     *
     * @return Folder
     */
    public function addChild(self $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * @param Folder $children
     *
     * @return Folder
     */
    public function removeChild(self $children)
    {
        $this->children->removeElement($children);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Folder|null $parent
     *
     * @return Folder
     */
    public function setParent(?self $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Folder|null
     */
    public function getParent()
    {
        return $this->parent;
    }
}
