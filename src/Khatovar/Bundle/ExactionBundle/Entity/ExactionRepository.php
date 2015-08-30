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

namespace Khatovar\Bundle\ExactionBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Exaction repository
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ExactionRepository extends EntityRepository
{
    /**
     * Get exactions by year.
     *
     * @param int $year
     *
     * @return Exaction[]
     */
    public function getExactionsByYear($year)
    {
        $query = $this->createQueryBuilder('pe')
            ->where('pe.start >= :start AND pe.start <= :end')
            ->setParameter('start', new \DateTime($year . '-01-01'))
            ->setParameter('end', new \DateTime($year . '-12-31'))
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Get all exactions to come.
     *
     * @return Exaction[]
     */
    public function getFutureExactions()
    {
        $query = $this->createQueryBuilder('pf')
            ->where('pf.start > :start')
            ->setParameter('start', new \DateTime())
            ->getQuery();

        return $query->getResult();
    }
}
