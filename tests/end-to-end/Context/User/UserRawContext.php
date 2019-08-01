<?php

declare(strict_types=1);

/*
 * This file is part of Khatovar.
 *
 * Copyright (c) 2019 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Tests\EndToEnd\Context\User;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\RawMinkContext;
use Webmozart\Assert\Assert;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserRawContext extends RawMinkContext
{
    protected function logAsUserWithPassword(string $username, string $password): void
    {
        $this->page()->fillField('Nom d\'utilisateur', $username);
        $this->page()->fillField('Mot de passe', $password);
        $this->page()->pressButton('Connexion');
    }

    protected function page(): DocumentElement
    {
        return $this->getSession()->getPage();
    }

    protected function assertPageContainsText(string $text): void
    {
        $this->assertSession()->pageTextContains($text);
    }

    protected function assertPath(string $path): void
    {
        $this->assertSession()->addressEquals($this->locatePath($path));
    }

    protected function fillFormFieldsAndValidateWithAction(array $formFieldsAndValues, string $action): void
    {
        foreach ($formFieldsAndValues as $field => $value) {
            $this->page()->fillField($field, $value);
        }

        $this->page()->pressButton($action);
    }

    protected function followActionLinkForUserRaw(string $action, string $username): void
    {
        $row = $this->findUserRowByText($username);
        $link = $row->findLink($action);

        Assert::notNull($link, 'Cannot find link in row with text '.$action);
        $link->click();
    }

    protected function pressActionButtonForUserRow(string $action, string $username): void
    {
        $row = $this->findUserRowByText($username);
        $button = $row->findButton($action);

        Assert::notNull($button, 'Cannot find button in row with text '.$action);
        $button->press();
    }

    private function findUserRowByText(string $username): NodeElement
    {
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $username));

        Assert::notNull($row, 'Cannot find a table row with username '.$username);

        return $row;
    }
}
