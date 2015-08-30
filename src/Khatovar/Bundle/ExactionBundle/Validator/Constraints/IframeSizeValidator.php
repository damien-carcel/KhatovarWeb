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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks that the size set in the iframe is 300x300.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class IframeSizeValidator extends ConstraintValidator
{
    const IFRAME_EXPECTED_WIDTH = 300;

    const IFRAME_EXPECTED_HEIGHT = 300;

    /**
     * {@inheritdoc}
     */
    public function validate($iframe, Constraint $constraint)
    {
        $dimensions = $this->getIframeDimensions($iframe);

        if (empty($dimensions)) {
            $this->context->buildViolation($constraint->noSizeMessage)->addViolation();
        } elseif (static::IFRAME_EXPECTED_WIDTH != $dimensions['width'] ||
            static::IFRAME_EXPECTED_HEIGHT != $dimensions['height']
        ) {
            $this->context->buildViolation($constraint->wrongSizeMessage)
                ->setParameter('%width%', $dimensions['width'])
                ->setParameter('%height%', $dimensions['height'])
                ->addViolation();
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
