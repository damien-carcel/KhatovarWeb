<?php

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

namespace Khatovar\Bundle\WebBundle\Twig;

/**
 * Twig extension that normalizes dates.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class DateNormalizer extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'khatovar_normalize_date',
                [$this, 'normalize'],
                ['is_sage' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'khatovar_normalize_date_with_year',
                [$this, 'normalizeWithYear'],
                ['is_sage' => ['html']]
            ),
        ];
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
                if (1 === ((int) $this->getDay($end)) - ((int) $this->getDay($start))) {
                    $linkWord = 'et';
                } else {
                    $linkWord = 'au';
                }

                $normalizedDate = sprintf(
                    '%s %s %s %s',
                    $this->getDay($start),
                    $linkWord,
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
        if ('1' === $date->format('d')) {
            return '1er';
        }

        return $date->format('d');
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
        $frenchMonths = [
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
            'décembre',
        ];

        return $frenchMonths[((int) $date->format('m')) - 1];
    }
}
