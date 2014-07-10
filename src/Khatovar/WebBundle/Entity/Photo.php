<?php

namespace Khatovar\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Photo
 *
 * @ORM\Table(name="khatovar_web_photos")
 * @ORM\Entity(repositoryClass="Khatovar\WebBundle\Entity\PhotoRepository")
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    private $alt;

    /**
     * @ORM\ManyToOne(targetEntity="Khatovar\WebBundle\Entity\Homepage", inversedBy="photos")
     */
    private $homepage;

    /**
     * @ORM\ManyToOne(targetEntity="Khatovar\WebBundle\Entity\Member", inversedBy="photos")
     */
    private $member;


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
     * @return Photo
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
     * Set homepage
     *
     * @param \Khatovar\WebBundle\Entity\Homepage $homepage
     * @return Photo
     */
    public function setHomepage(\Khatovar\WebBundle\Entity\Homepage $homepage = null)
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
    public function setMember(\Khatovar\WebBundle\Entity\Member $member = null)
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
}
