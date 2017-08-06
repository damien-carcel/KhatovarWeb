<?php

/*
 * This file is part of CarcelDocumentsBundle.
 *
 * Copyright (c) 2017 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Context\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Khatovar\Bundle\DocumentsBundle\Entity\Folder;

/**
 * The way fixtures are handled is quite fragile, as we get folders by name, so
 * there cannot be two folders with the same name.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class LoadFolderData implements FixtureInterface
{
    /** @var array */
    protected static $foldersData = [
        [
            'name' => 'A folder at root',
        ],
        [
            'name' => 'An other folder without parent',
        ],
        [
            'name' => 'A folder inside a folder',
            'parent' => 'A folder at root',
        ],
        [
            'name' => 'Another folder inside a folder',
            'parent' => 'A folder at root',
        ],
        [
            'name' => 'Folder inception',
            'parent' => 'A folder inside a folder',
        ],
    ];

    /** @var Folder[] */
    protected $createdFolders = [];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (static::$foldersData as $folderData) {
            $this->createdFolders[$folderData['name']] = $this->createFolder($folderData);
        }

        foreach ($this->createdFolders as $folder) {
            $manager->persist($folder);
        }

        $manager->flush();
    }

    /**
     * @param array $folderData
     *
     * @return Folder
     */
    private function createFolder(array $folderData)
    {
        $folder = new Folder();

        $folder->setName($folderData['name']);
        if (isset($folderData['parent']) && isset($this->createdFolders[$folderData['parent']])) {
            $folder->setParent($this->createdFolders[$folderData['parent']]);
        }

        return $folder;
    }
}
