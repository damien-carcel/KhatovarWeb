<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2017 Damien Carcel (https://github.com/damien-carcel)
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
    /** @const array */
    private const FOLDER_DATA = [
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
    public function load(ObjectManager $manager): void
    {
        foreach (static::FOLDER_DATA as $folderData) {
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
    private function createFolder(array $folderData): Folder
    {
        $folder = new Folder();

        $folder->setName($folderData['name']);
        if (isset($folderData['parent']) && isset($this->createdFolders[$folderData['parent']])) {
            $folder->setParent($this->createdFolders[$folderData['parent']]);
        }

        return $folder;
    }
}
