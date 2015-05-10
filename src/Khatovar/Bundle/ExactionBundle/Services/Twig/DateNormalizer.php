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

namespace Khatovar\Bundle\ExactionBundle\Services\Twig;

/**
 * Date normalizer.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class DateNormalizer extends \Twig_Extension
{
    /**
     * Return the filters defined in this class.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'normalize_date',
                array($this, 'normalize'),
                array('is_sage' => array('html'))
            ),
        );
    }

    /**
     * Return the formatted date of the exaction.
     *
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return string
     */
    public function normalize(\DateTime $start, \DateTime $end)
    {
        if ($start !== $end) {
            if ($start->format('m') !== $end->format('m')) {
                $normalizedDate = $start->format('j F') . ' au ' . $end->format('j F');
            } else {
                $normalizedDate = $start->format('j') . ' au ' . $end->format('j F');
            }
        } else {
            $normalizedDate = $start->format('j F');
        }

        return $normalizedDate;
    }

    /**
     * Return the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'khatovar_date_normalizer';
    }
}
