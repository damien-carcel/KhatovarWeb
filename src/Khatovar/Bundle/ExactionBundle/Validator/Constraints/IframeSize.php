<?php
/**
 * This file is part of KhatovarWeb.
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
 *
 * @copyright   Copyright (C) Damien Carcel (https://github.com/damien-carcel)
 *
 * @see        https://github.com/damien-carcel/KhatovarWeb
 *
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\ExactionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the size set in the iframe is 300x300.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class IframeSize extends Constraint
{
    /** @var string */
    public $noSizeMessage = 'Votre carte ne semble pas avoir de taille définie. Veuillez vérifier le code saisi.';

    /** @var string */
    public $wrongSizeMessage = 'Les cartes doivent faire 300x300. Celle que vous avez saisie fait %width%x%height%.';
}
