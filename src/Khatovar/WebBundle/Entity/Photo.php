<?php

namespace Khatovar\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Photo
 *
 * @ORM\Table(name="khatovar_web_photos")
 * @ORM\Entity(repositoryClass="Khatovar\WebBundle\Entity\PhotoRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * The alternate text to display.
     *
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    private $alt;

    /**
     * The CSS class used for resizing.
     *
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    private $size;

    /**
     * The section of the website the photo is attached to.
     *
     * @var array
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="entity", type="array")
     */
    private $entity;

    /**
     * The location of the file on the server.
     *
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * Temporary attribute to remember the file path when deleting it.
     *
     * @var string
     */
    private $temp;

    /**
     * @var UploadedFile
     *
     * @Assert\File(maxSize="8000000")
     */
    private $file;


    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        // If there already is a path, it is stored in the $temp
        // attribute in case of a future deletion
        if ($this->path) {
            $this->temp = $this->path;
            $this->path = null;
        }
    }

    /**
     * Set the name of the file before uploading it.
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (!is_null($this->file)) {
            // The photo is named according to the time stamp of the upload.
            $this->path = 'photo-' . time() . '.'
                . $this->getFile()->guessExtension();
        }
    }

    /**
     * Upload the file on the server.
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (is_null($this->file)) {
            return;
        }

        $this->file->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image, and then delete it
        if (isset($this->temp)) {
            unlink($this->getUploadRootDir().'/'.$this->temp);
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }

    /**
     * Return the absolute path to the file.
     *
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return is_null($this->path)
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    /**
     * Return a relative path to the file.
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return is_null($this->path)
            ? null
            : '/' . $this->getUploadDir() . '/' . $this->path;
    }

    /**
     * Return the absolute path of the directory containing the file.
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../www/' . $this->getUploadDir();
    }

    /**
     * Return the relative path of the directory containing the file.
     * Useful for linking the file inside html page.
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploaded/photos';
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
     * Set size
     *
     * @param string $size
     * @return Photo
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
     * Set path
     *
     * @param string $path
     * @return Photo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set entity
     *
     * @param array $entity
     * @return Photo
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return array
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
