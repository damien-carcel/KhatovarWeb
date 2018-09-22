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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks that the size set in the iframe is 300x300.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class IframeSizeValidator extends ConstraintValidator
{
    public const IFRAME_EXPECTED_WIDTH = 300;

    public const IFRAME_EXPECTED_HEIGHT = 300;

    /**
     * {@inheritdoc}
     */
    public function validate($iframe, Constraint $constraint): void
    {
        if (null !== $iframe) {
            $dimensions = $this->getIframeDimensions($iframe);

            if (empty($dimensions)) {
                $this->context->buildViolation($constraint->noSizeMessage)->addViolation();
            } elseif (static::IFRAME_EXPECTED_WIDTH !== $dimensions['width'] ||
                static::IFRAME_EXPECTED_HEIGHT !== $dimensions['height']
            ) {
                $this->context->buildViolation($constraint->wrongSizeMessage)
                    ->setParameter('%width%', $dimensions['width'])
                    ->setParameter('%height%', $dimensions['height'])
                    ->addViolation();
            }
        }
    }

    /**
     * Perform a regular expression to to see if iframe dimensions are correct.
     *
     * @param string $iframe
     *
     * @return string
     */
    protected function getIframeDimensions($iframe)
    {
        $pattern = '/(width)="(?P<width>\d+)" (height)="(?P<height>\d+)"/';

        preg_match($pattern, $iframe, $matches);

        return $matches;
    }
}
