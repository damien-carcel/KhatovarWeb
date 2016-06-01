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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Exaction repository
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ExactionRepository extends EntityRepository
{
    /**
     * Finds one exaction by its ID or throws a 404.
     *
     * @param int $id
     *
     * @return Exaction
     *
     * @throws NotFoundHttpException
     */
    public function findByIdOr404($id)
    {
        $exaction = $this->find($id);

        if (!$exaction) {
            throw new NotFoundHttpException('Impossible de trouver l\'exaction.');
        }

        return $exaction;
    }

    /**
     * Gets exactions by year.
     *
     * @param int $year
     *
     * @return Exaction[]
     *
     * @throws NotFoundHttpException
     */
    public function getExactionsByYear($year)
    {
        $query = $this->createQueryBuilder('pe')
            ->where('pe.start >= :start AND pe.start <= :end')
            ->setParameter('start', new \DateTime($year . '-01-01'))
            ->setParameter('end', new \DateTime($year . '-12-31'))
            ->andWhere('pe.start <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('pe.start', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Gets all exactions to come.
     *
     * @return Exaction[]
     */
    public function getFutureExactions()
    {
        $query = $this->createQueryBuilder('pf')
            ->where('pf.start > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('pf.start', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
