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
    /** @var Folder[] */
    private $createdFolders = [];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $foldersData = $this->getFoldersDataFromJson();

        foreach ($foldersData as $folderData) {
            $this->createdFolders[$folderData['name']] = $this->createFolder($folderData);
        }

        foreach ($this->createdFolders as $folder) {
            $manager->persist($folder);
        }

        $manager->flush();
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    private function getFoldersDataFromJson(): array
    {
        $pathToFoldersData = __DIR__.'/../folders.json';
        if (!file_exists($pathToFoldersData)) {
            throw new \Exception(sprintf('There is no file at path "%s"', $pathToFoldersData));
        }

        $jsonFoldersData = file_get_contents($pathToFoldersData);

        return json_decode($jsonFoldersData, true);
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
        if (null !== $folderData['parent'] && isset($this->createdFolders[$folderData['parent']])) {
            $folder->setParent($this->createdFolders[$folderData['parent']]);
        }

        return $folder;
    }
}
