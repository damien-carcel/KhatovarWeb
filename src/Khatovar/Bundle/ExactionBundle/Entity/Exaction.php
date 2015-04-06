<?php

namespace Khatovar\Bundle\ExactionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Exaction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Khatovar\Bundle\ExactionBundle\Entity\ExactionRepository")
 */
class Exaction
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}

