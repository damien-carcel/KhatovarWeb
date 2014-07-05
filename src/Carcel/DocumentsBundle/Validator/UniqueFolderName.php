<?php
/**
 *
 * This file is part of Documents.
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
 * @link        https://github.com/damien-carcel/Documents
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Carcel\DocumentsBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Prevent that in a given folder, a new folder has the same name that
 * an existing one.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Carcel\DocumentsBundle\Validator
 * @Annotation
 */
class UniqueFolderName extends Constraint
{
    public $message = 'Un dossier présent dans le dossier actuel porte déjà le nom « %string% ».';

    /**
     * Add the Validator class.
     *
     * @return string
     */
    public function validateBy()
    {
        return get_class($this) . 'Validator';
    }

    /**
     * The constraint apply to the all Folder class.
     *
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
