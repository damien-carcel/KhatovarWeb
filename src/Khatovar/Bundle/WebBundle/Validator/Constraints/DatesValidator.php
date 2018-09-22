<?php

declare(strict_types=1);

/**
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2015 Damien Carcel (https://github.com/damien-carcel)
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

namespace Khatovar\Bundle\WebBundle\Validator\Constraints;

use Khatovar\Bundle\WebBundle\Entity\Exaction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks that exaction starting date is older or at least the same than
 * exaction ending date.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class DatesValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($exaction, Constraint $constraint): void
    {
        if ($exaction instanceof Exaction) {
            if ($exaction->getStart() > $exaction->getEnd()) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}
