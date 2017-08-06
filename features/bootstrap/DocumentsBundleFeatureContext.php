<?php

/*
 * This file is part of CarcelDocumentsBundle.
 *
 * Copyright (c) 2017 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Context;

use Behat\MinkExtension\Context\MinkContext;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Defines application features from the specific context.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class DocumentsBundleFeatureContext extends MinkContext
{
    /** @var ContainerInterface */
    protected $container;

    /** @var SessionInterface */
    protected $session;

    /**
     * @param SessionInterface   $session
     * @param ContainerInterface $container
     */
    public function __construct(SessionInterface $session, ContainerInterface $container)
    {
        $this->session = $session;
        $this->container = $container;

        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        $this->removeUploadedFiles();
    }

    /**
     * @BeforeScenario @fixtures-minimal
     */
    public function loadFixturesWithOnlyUsers()
    {
        $userFixtures = $this->container
                ->getParameter('kernel.project_dir').'/features/bootstrap/DataFixtures/ORM/LoadUserData.php';

        $this->loadDoctrineFixtures([
            'files' => [$userFixtures],
        ]);
    }

    /**
     * @BeforeScenario @fixtures-with-folders
     */
    public function loadFixturesWithFoldersAndFiles()
    {
        $fixtures = $this->container
                ->getParameter('kernel.project_dir').'/features/bootstrap/DataFixtures/ORM';

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
    public function iSetRoleForUser($role, $username)
    {
        $user = $this->container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CarcelUserBundle:User')
            ->findOneBy(['username' => $username]);

        if (null === $user) {
            throw new  \InvalidArgumentException(
                sprintf('The user with the name %s does not exists', $username),
                0,
                static::class
            );
        }

        $this->container->get('carcel_user.manager.users')->setRole($user, ['roles' => $role]);
    }

    /**
     * Removes all files and folders from a given directory.
     *
     * @param string|null $directory
     */
    private function removeUploadedFiles($directory = null)
    {
        $root = false;
        if (null === $directory) {
            $directory = $this->container->getParameter('khatovar_document.upload_dir');
            $root = true;
        }

        if (is_dir($directory)) {
            $objects = scandir($directory);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
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
    private function loadDoctrineFixtures(array $fixturePaths)
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

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
