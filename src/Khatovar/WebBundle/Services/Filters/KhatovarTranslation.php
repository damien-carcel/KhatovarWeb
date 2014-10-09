<?php
/**
 *
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
 * @link        https://github.com/damien-carcel/KhatovarWeb
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\WebBundle\Services\Filters;

/**
 * Perform some transformations on html code before display or saving.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Services\Translation
 */
class KhatovarTranslation
{
    /**
     * Look for special tags and transform it in twig syntaxe.
     *
     * @param string $text The text to transform.
     * @return string
     */
    public function imageTranslate($text)
    {
        return $text;
    }

    /**
     * Replace certain spaces with non-breaking spaces.
     *
     * @param string $text The text to transform.
     * @return string
     */
    public function specialSpacesTranslate($text)
    {
        return $text;
    }
}
