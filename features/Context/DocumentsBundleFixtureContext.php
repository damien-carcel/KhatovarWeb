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

namespace Context;

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Khatovar\Component\User\Application\Command\SetRole;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines application features from the specific context.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class DocumentsBundleFixtureContext implements Context
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $entityManager = $this->container->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        $this->removeUploadedFiles();
    }

    /**
     * @BeforeScenario @fixtures-minimal
     */
    public function loadFixturesWithOnlyUsers(): void
    {
        $userFixtures = $this->container
                ->getParameter('kernel.project_dir').'/features/Context/DataFixtures/ORM/LoadUserData.php';

        $this->loadDoctrineFixtures([
            'files' => [$userFixtures],
        ]);
    }

    /**
     * @BeforeScenario @fixtures-with-folders
     */
    public function loadFixturesWithFolders(): void
    {
        $fixtures = $this->container
                ->getParameter('kernel.project_dir').'/features/Context/DataFixtures/ORM';

        $this->loadDoctrineFixtures([
            'directories' => [$fixtures],
        ]);
    }

    /**
     * Sets a new role to a user.
     *
     * @param string $role
     * @param string $username
     *
     * @Given /^I set role "(?P<role>[^"]*)" for user "(?P<username>[^"]*)"$/
     */
    public function iSetRoleForUser(string $role, string $username): void
    {
        $user = $this->container
            ->get('Khatovar\Bundle\UserBundle\Entity\Repository\UserRepository')
            ->get($username);

        $this->container
            ->get('Khatovar\Component\User\Application\Command\SetRoleHandler')
            ->setRole(new SetRole($user, ['roles' => $role]));
    }

    /**
     * Removes all files and folders from a given directory.
     *
     * @param string|null $directory
     */
    private function removeUploadedFiles(string $directory = null): void
    {
        $root = false;
        if (null === $directory) {
            $directory = $this->container->getParameter('khatovar_document.upload_dir');
            $root = true;
        }

        if (is_dir($directory)) {
            $objects = scandir($directory);
            foreach ($objects as $object) {
                if ('.' != $object && '..' != $object) {
                    if (is_dir($directory.'/'.$object)) {
                        $this->removeUploadedFiles($directory.'/'.$object);
                    } else {
                        unlink($directory.'/'.$object);
                    }
                }
            }

            if (!$root) {
                rmdir($directory);
            }
        }
    }

    /**
     * Loads Doctrine data fixtures, from directories and/or files.
     *
     * @param array $fixturePaths
     */
    private function loadDoctrineFixtures(array $fixturePaths): void
    {
        $entityManager = $this->container->get('doctrine')->getManager();

        $loader = new DataFixturesLoader($this->container);

        if (isset($fixturePaths['directories']) && is_array($fixturePaths['directories'])) {
            foreach ($fixturePaths['directories'] as $directory) {
                $loader->loadFromDirectory($directory);
            }
        }

        if (isset($fixturePaths['files']) && is_array($fixturePaths['files'])) {
            foreach ($fixturePaths['files'] as $file) {
                $loader->loadFromFile($file);
            }
        }

        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}
