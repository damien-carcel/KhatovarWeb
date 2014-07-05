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

use Carcel\DocumentsBundle\Entity\Folder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * When creating a new folder, check if another one with the same name
 * does not already exists in the same parent folder.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Carcel\DocumentsBundle\Validator
 */
class UniqueFolderNameValidator extends ConstraintValidator
{
    /**
     * Validate the constraint.
     *
     * @param Folder $folder The name of the folder to test.
     * @param Constraint $constraint The constraint applied to $value.
     */
    public function validate($folder, Constraint $constraint)
    {
        $neighbors = $folder->getParent()->getChildren();

        foreach ($neighbors as $neighbor) {
            if ($neighbor->getName() == $folder->getName() and $neighbor->getId() != $folder->getId()) {
                $this->context->addViolation(
                    $constraint->message,
                    array('%string%' => $folder->getName())
                );
            }
        }
    }
}
