<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2019 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Tests\EndToEnd\Context\User;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AuthenticationContext extends RawMinkContext
{
    /**
     * @param string $username
     *
     * @throws ElementNotFoundException
     *
     * @Given I am logged as :username
     */
    public function iAmLoggedAs(string $username): void
    {
        $this->visitPath('login');

        $this->page()->fillField('Nom d\'utilisateur', $username);
        $this->page()->fillField('Mot de passe', $username);
        $this->page()->pressButton('Connexion');
    }

    /**
     * @return DocumentElement
     */
    private function page(): DocumentElement
    {
        return $this->getSession()->getPage();
    }
}
