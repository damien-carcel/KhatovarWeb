<?php

declare(strict_types=1);

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
class Exaction
{
    /** @var int */
    protected $id;

    /**
     * The name of the festival.
     *
     * @var string
     */
    protected $name;

    /**
     * Where the festival take place.
     *
     * @var string
     */
    protected $place;

    /**
     * When the festival starts.
     *
     * @var \DateTime
     */
    protected $start;

    /**
     * When the festival ends.
     *
     * @var \DateTime
     */
    protected $end;

    /**
     * Photos of the festival.
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $photos;

    /**
     * A map in an iframe to locate the festival.
     *
     * @var string
     */
    protected $map;

    /**
     * The announcement of the festival.
     *
     * @var string
     */
    protected $introduction;

    /**
     * Useful links (festival website, town website…).
     *
     * @var array
     */
    protected $links;

    /**
     * An emblematic photo of the festival.
     *
     * @var Photo
     */
    protected $image;

    /**
     * An abstract of what happened on the festival.
     *
     * @var string
     */
    protected $abstract;

    /**
     * Description of the emblematic photo.
     *
     * @var string
     */
    protected $imageStory;

    /**
     * Is there an abstract or only photos?
     *
     * @var bool
     */
    protected $onlyPhotos;

    /**
     * Create an instance of Exaction.
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->onlyPhotos = false;
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
     * @return string
     */
    public function getCompleteName()
    {
        return sprintf(
            '%s, %s, %s',
            $this->name,
            $this->place,
            $this->start->format('Y')
        );
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
     * Set name.
     *
     * @param string $name
     *
     * @return Exaction
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

    /**
     * Set place.
     *
     * @param string $place
     *
     * @return Exaction
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set map.
     *
     * @param string $map
     *
     * @return Exaction
     */
    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map.
     *
     * @return string
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set introduction.
     *
     * @param string $introduction
     *
     * @return Exaction
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction.
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set links.
     *
     * @param array $links
     *
     * @return Exaction
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Get links.
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set abstract.
     *
     * @param string $abstract
     *
     * @return Exaction
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * Get abstract.
     *
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set imageStory.
     *
     * @param string $imageStory
     *
     * @return Exaction
     */
    public function setImageStory($imageStory)
    {
        $this->imageStory = $imageStory;

        return $this;
    }

    /**
     * Get imageStory.
     *
     * @return string
     */
    public function getImageStory()
    {
        return $this->imageStory;
    }

    /**
     * Set onlyPhotos.
     *
     * @param bool $onlyPhotos
     *
     * @return Exaction
     */
    public function setOnlyPhotos($onlyPhotos)
    {
        $this->onlyPhotos = $onlyPhotos;

        return $this;
    }

    /**
     * Get onlyPhotos.
     *
     * @return bool
     */
    public function isOnlyPhotos()
    {
        return $this->onlyPhotos;
    }

    /**
     * Add photo.
     *
     * @return Exaction
     */
    public function addPhoto(Photo $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo.
     *
     * @return Exaction
     */
    public function removePhoto(Photo $photo)
    {
        $this->photos->removeElement($photo);

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
     * Set image.
     *
     * @param Photo $image
     *
     * @return Exaction
     */
    public function setImage(Photo $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return Photo
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set start.
     *
     * @param \DateTime $start
     *
     * @return Exaction
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start.
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end.
     *
     * @param \DateTime $end
     *
     * @return Exaction
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end.
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }
}
