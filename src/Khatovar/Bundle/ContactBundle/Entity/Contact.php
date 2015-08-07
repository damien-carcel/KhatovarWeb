<?php

namespace Khatovar\Bundle\ContactBundle\Entity;

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
    public function setVisitCard(Photo $visitCard)
    {
        $this->visitCard = $visitCard;

        return $this;
    }
}
