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
 * Checks that the exaction "iframe" field indeed contains an iframe.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ContainsIframeValidator extends ConstraintValidator
{
    /** @staticvar string */
    const IFRAME_OPENING_TAG = '<iframe src=';

    /** @staticvar string */
    const IFRAME_CLOSING_TAG = '</iframe>';

    /**
     * {@inheritdoc}
     */
    public function validate($iframe, Constraint $constraint)
    {

        if (null != $iframe) {
            if (strlen($iframe) < strlen(static::IFRAME_OPENING_TAG . static::IFRAME_CLOSING_TAG)) {
                $this->context->buildViolation($constraint->messageLength)->addViolation();
            } else {
                $iframeOpening = substr(
                    $iframe,
                    0,
                    strlen(static::IFRAME_OPENING_TAG)
                );
                $iframeClosing = substr(
                    $iframe,
                    -strlen(static::IFRAME_CLOSING_TAG)
                );

                if ($iframeOpening !== static::IFRAME_OPENING_TAG || $iframeClosing !== static::IFRAME_CLOSING_TAG) {
                    $this->context->buildViolation($constraint->messageContent)->addViolation();
                }
            }
        }
    }
}
