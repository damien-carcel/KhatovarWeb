<?php

declare(strict_types=1);

/**
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) Damien Carcel (https://github.com/damien-carcel)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version of the License, or
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

use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class DocumentsBundleDownloadContext extends RawMinkContext
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Asserts that a page.
     *
     * @param string $fileName
     *
     * @When /^the current page should be the file "(?P<fileName>[^"]*)"$/
     */
    public function iShouldBeAbleToDownloadAFile(string $fileName)
    {
        $content = $this->getSession()->getPage()->getContent();

        $file = $this->container->get('khatovar_documents.repositories.file')->findOneBy(['name' => $fileName]);
        $path = $file->getAbsolutePath();
        $rawFile = file_get_contents($path);

        Assert::same($rawFile, $content, sprintf(
            'Expected to download file "%s", get the following content "%s"',
            $fileName,
            $content
        ));
    }
}
