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

use Carcel\Bundle\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Khatovar\Bundle\PhotoBundle\Entity\Photo;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class Member
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $slug;

    /** @var string */
    protected $rank;

    /** @var string */
    protected $quote;

    /** @var string */
    protected $skill;

    /** @var string */
    protected $age;

    /** @var string */
    protected $size;

    /** @var string */
    protected $weight;

    /** @var string */
    protected $strength;

    /** @var string */
    protected $weakness;

    /** @var string */
    protected $story;

    /** @var string */
    protected $active;

    /** @var Photo */
    protected $portrait;

    /** @var User */
    protected $owner;

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
     * Allow to save only the entity's ID in database as a string when
     * using entity type in forms.
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
     * Set name.
     *
     * @param string $name
     *
     * @return Member
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return Member
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set rank.
     *
     * @param string $rank
     *
     * @return Member
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set quote.
     *
     * @param string $quote
     *
     * @return Member
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote.
     *
     * @return string
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set skill.
     *
     * @param string $skill
     *
     * @return Member
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill.
     *
     * @return string
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Set age.
     *
     * @param string $age
     *
     * @return Member
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age.
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set size.
     *
     * @param string $size
     *
     * @return Member
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set weight.
     *
     * @param string $weight
     *
     * @return Member
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set strength.
     *
     * @param string $strength
     *
     * @return Member
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;

        return $this;
    }

    /**
     * Get strength.
     *
     * @return string
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * Set weakness.
     *
     * @param string $weakness
     *
     * @return Member
     */
    public function setWeakness($weakness)
    {
        $this->weakness = $weakness;

        return $this;
    }

    /**
     * Get weakness.
     *
     * @return string
     */
    public function getWeakness()
    {
        return $this->weakness;
    }

    /**
     * Set story.
     *
     * @param string $story
     *
     * @return Member
     */
    public function setStory($story)
    {
        $this->story = $story;

        return $this;
    }

    /**
     * Get story.
     *
     * @return string
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Member
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
     * Set portrait.
     *
     * @param Photo $portrait
     *
     * @return Member
     */
    public function setPortrait(Photo $portrait = null)
    {
        $this->portrait = $portrait;

        return $this;
    }

    /**
     * Get portrait.
     *
     * @return Photo
     */
    public function getPortrait()
    {
        return $this->portrait;
    }

    /**
     * Set owner.
     *
     * @param User $owner
     *
     * @return Member
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner.
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add photos.
     *
     * @param Photo $photos
     *
     * @return Member
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
     * @return Member
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
