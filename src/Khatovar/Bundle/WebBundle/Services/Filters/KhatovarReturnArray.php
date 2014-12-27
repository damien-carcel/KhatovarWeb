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

namespace Khatovar\Bundle\WebBundle\Services\Filters;

use Doctrine\ORM\EntityManager;
use Khatovar\WebBundle\Entity\Homepage;

/**
 * Return some data as array.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\WebBundle\Services\Filters
 */
class KhatovarReturnArray
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Return all entries for a given entity as a ID=>Name array.
     *
     * @param string $entity The entity we want to retrieve entries..
     * @return array
     */
    public function returnArray($entity)
    {
        $entries = $this->em
            ->getRepository('KhatovarWebBundle:' . ucfirst($entity))
            ->findAll();

        $result = array();
        foreach ($entries as $entry) {
            /**
             * @var Homepage $entry
             */
            $result[$entry->getId()] = $entry->getName();
        }

        return $result;
    }
}
