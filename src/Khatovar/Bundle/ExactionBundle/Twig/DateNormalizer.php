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

namespace Khatovar\Bundle\ExactionBundle\Twig;

/**
 * Date normalizer.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class DateNormalizer extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'khatovar_normalize_date',
                array($this, 'normalize'),
                array('is_sage' => array('html'))
            ),
            new \Twig_SimpleFunction(
                'khatovar_normalize_date_with_year',
                array($this, 'normalizeWithYear'),
                array('is_sage' => array('html'))
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_date_normalizer';
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
        if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
            if ($start->format('m') !== $end->format('m')) {
                $normalizedDate = sprintf(
                    '%s %s au %s %s',
                    $this->getDay($start),
                    $this->translateMonth($start),
                    $this->getDay($end),
                    $this->translateMonth($end)
                );
            } else {
                $normalizedDate = sprintf(
                    '%s au %s %s',
                    $this->getDay($start),
                    $this->getDay($end),
                    $this->translateMonth($end)
                );
            }
        } else {
            $normalizedDate = sprintf('%s %s', $this->getDay($start), $this->translateMonth($start));
        }

        return $normalizedDate;
    }

    /**
     * Return the formatted date of the exaction with the year.
     *
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return string
     */
    public function normalizeWithYear(\DateTime $start, \DateTime $end)
    {
        return sprintf(
            '%s %s',
            $this->normalize($start, $end),
            $end->format('Y')
        );
    }

    /**
     * Return a formatted DateTime day.
     *
     * @param \DateTime $date
     *
     * @return string
     */
    protected function getDay(\DateTime $date)
    {
        if ($date->format('d') == '1') {
            return '1er';
        } else {
            return $date->format('d');
        }
    }

    /**
     * Return the french translation of a DateTime month.
     *
     * @param \DateTime $date
     *
     * @return string
     */
    protected function translateMonth(\DateTime $date)
    {
        $frenchMonths = array(
            'janvier',
            'février',
            'mars',
            'avril',
            'mai',
            'juin',
            'juillet',
            'août',
            'septembre',
            'octobre',
            'novembre',
            'décembre'
        );

        return $frenchMonths[((int) $date->format('m')) - 1];
    }
}
