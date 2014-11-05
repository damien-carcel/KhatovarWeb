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

namespace Khatovar\WebBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Member
 *
 * @ORM\Table(name="khatovar_web_members")
 * @ORM\Entity(repositoryClass="Khatovar\WebBundle\Entity\MemberRepository")
 */
class Member
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToOne(targetEntity="Khatovar\WebBundle\Entity\Photo")
     * @ORM\JoinColumn(nullable=true)
     */
    private $portrait;

    /**
     * @var string
     *
     * @ORM\Column(name="rank", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $rank;

    /**
     * @var string
     *
     * @ORM\Column(name="quote", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $quote;

    /**
     * @var string
     *
     * @ORM\Column(name="skill", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $skill;

    /**
     * @var string
     *
     * @ORM\Column(name="age", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="strength", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $strength;

    /**
     * @var string
     *
     * @ORM\Column(name="weakness", type="string", length=255)
     * @Assert\Length(max="255")
     */
    private $weakness;

    /**
     * @var string
     *
     * @ORM\Column(name="story", type="text")
     * @Assert\NotBlank()
     */
    private $story;

    /**
     * @ORM\OneToOne(targetEntity="Carcel\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $owner;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     * @Assert\Length(max="255")
     */
    private $active;


    /**
     * Allow to save only the ID the entity in database as a string
     * when using entity type in forms.
     *
     * @return string
     */
    public function __toString()
    {
        return strval($this->id);
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
     * @return Member
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
     * Set slug
     *
     * @param string $slug
     * @return Member
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set rank
     *
     * @param string $rank
     * @return Member
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return string 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set quote
     *
     * @param string $quote
     * @return Member
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return string 
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set skill
     *
     * @param string $skill
     * @return Member
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return string 
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Set age
     *
     * @param string $age
     * @return Member
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return string 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return Member
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set weight
     *
     * @param string $weight
     * @return Member
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set strength
     *
     * @param string $strength
     * @return Member
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;

        return $this;
    }

    /**
     * Get strength
     *
     * @return string 
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * Set weakness
     *
     * @param string $weakness
     * @return Member
     */
    public function setWeakness($weakness)
    {
        $this->weakness = $weakness;

        return $this;
    }

    /**
     * Get weakness
     *
     * @return string 
     */
    public function getWeakness()
    {
        return $this->weakness;
    }

    /**
     * Set story
     *
     * @param string $story
     * @return Member
     */
    public function setStory($story)
    {
        $this->story = $story;

        return $this;
    }

    /**
     * Get story
     *
     * @return string 
     */
    public function getStory()
    {
        return $this->story;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Member
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set portrait
     *
     * @param \Khatovar\WebBundle\Entity\Photo $portrait
     * @return Member
     */
    public function setPortrait(\Khatovar\WebBundle\Entity\Photo $portrait = null)
    {
        $this->portrait = $portrait;

        return $this;
    }

    /**
     * Get portrait
     *
     * @return \Khatovar\WebBundle\Entity\Photo 
     */
    public function getPortrait()
    {
        return $this->portrait;
    }

    /**
     * Set owner
     *
     * @param \Carcel\UserBundle\Entity\User $owner
     * @return Member
     */
    public function setOwner(\Carcel\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Carcel\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
