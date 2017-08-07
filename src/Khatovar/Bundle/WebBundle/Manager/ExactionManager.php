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

namespace Khatovar\Bundle\WebBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\WebBundle\Entity\Exaction;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ExactionManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Return a sorted array of all years having exactions, most recent
     * first.
     *
     * @return array
     */
    public function getSortedYears()
    {
        $yearList = [];

        $exactions = $this->entityManager
            ->getRepository('KhatovarWebBundle:Exaction')
            ->findAll();

        foreach ($exactions as $exaction) {
            $now = new \DateTime();
            if ($exaction->getStart() <= $now) {
                $year = $exaction->getStart()->format('Y');
                if (!in_array($year, $yearList)) {
                    $yearList[] = $year;
                }
            }
        }

        arsort($yearList);

        return $yearList;
    }

    /**
     * Check if the exaction is already passed or still to come.
     *
     * @param Exaction $exaction
     *
     * @return bool return true if exaction is passed, false if not
     */
    public function isExactionPassed(Exaction $exaction)
    {
        $now = new \DateTime();

        if ($exaction->getStart() > $now) {
            return false;
        }

        return true;
    }
}
