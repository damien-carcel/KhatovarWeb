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

namespace Khatovar\Bundle\ExactionBundle\Validator\Constraints;

use Khatovar\Bundle\ExactionBundle\Entity\Exaction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Exaction dates validator.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class DatesValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($exaction, Constraint $constraint)
    {
        if ($exaction instanceof Exaction) {
            if ($exaction->getStart() > $exaction->getEnd()) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}
