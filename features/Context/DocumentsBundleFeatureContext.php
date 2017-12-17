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

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\MinkContext;
use PHPUnit\Framework\Assert;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines application features from the specific context.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class DocumentsBundleFeatureContext extends MinkContext
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
     * @param string $buttonText
     * @param string $rowText
     *
     * @Given /^I click on "(?P<buttonText>[^"]*)" in table row "(?P<rowText>[^"]*)"$/
     */
    public function iClickInTheRow(string $buttonText, string $rowText): void
    {
        $button = $this->findButtonForTableRow($buttonText, $rowText);

        $button->click();
    }

    /**
     * Checks that a specific table row contains a specific text.
     *
     * @param string $text
     * @param string $rowText
     *
     * @Then /^I should see "(?P<text>[^"]*)" in table row "(?P<rowText>[^"]*)"$/
     */
    public function iShouldSeeTheTextInTheTableRow(string $text, string $rowText): void
    {
        $row = sprintf('table tr:contains("%s")', $rowText);

        $this->assertElementContainsText($row, $text);
    }

    /**
     * Checks that a specific table row contains a specific text.
     *
     * @param string $buttonText
     * @param string $rowText
     *
     * @Then /^I should see the button "(?P<buttonText>[^"]*)" in table row "(?P<rowText>[^"]*)"$/
     */
    public function iShouldSeeTheButtonInTheTableRow(string $buttonText, string $rowText): void
    {
        $this->findButtonForTableRow($buttonText, $rowText);
    }

    /**
     * @param string $buttonText
     * @param string $rowText
     *
     * @return NodeElement
     */
    private function findButtonForTableRow(string $buttonText, string $rowText): NodeElement
    {
        $row = $this->findRowByText($rowText);

        $button = $row->findButton($buttonText);
        if (null === $button) {
            $button = $row->findLink($buttonText);
        }
        Assert::assertNotNull($button, sprintf('Cannot find a button "%s" in the row "%s"', $buttonText, $rowText));

        return $button;
    }

    /**
     * Finds a table row by its content.
     *
     * @param string $rowText
     *
     * @return NodeElement
     */
    private function findRowByText(string $rowText): NodeElement
    {
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        Assert::assertNotNull($row, sprintf('Cannot find a table row with "%s"', $rowText));

        return $row;
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
}
