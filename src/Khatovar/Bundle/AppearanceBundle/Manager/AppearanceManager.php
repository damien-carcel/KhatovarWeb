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

namespace Khatovar\Bundle\AppearanceBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\AppearanceBundle\Entity\Appearance;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Appearance manager.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AppearanceManager
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
     * Find an appearance, alongside its alphabetically next and
     * previous appearances.
     *
     * The 3 appearances are returned as an array, identified by the
     * keys "previous", "current", and "next". If previous and/or next
     * appearances don't exists, the array contain "null".
     *
     * @param string $slug
     *
     * @return Appearance[]
     *
     * @throws NotFoundHttpException
     */
    public function findWithNextAndPreviousOr404($slug)
    {
        $sortedAppearances = $this->entityManager
            ->getRepository('KhatovarAppearanceBundle:Appearance')
            ->findActiveSortedBySlug();

        $appearances = array();

        for ($position = 0; $position < count($sortedAppearances); $position++) {
            if ($slug === $sortedAppearances[$position]->getSlug()) {
                $appearances['current'] = $sortedAppearances[$position];

                if (isset($sortedAppearances[$position - 1])) {
                    $appearances['previous'] = $sortedAppearances[$position - 1];
                } else {
                    $appearances['previous'] = null;
                }

                if (isset($sortedAppearances[$position + 1])) {
                    $appearances['next'] = $sortedAppearances[$position + 1];
                } else {
                    $appearances['next'] = null;
                }

                break;
            }
        }

        if (empty($appearances)) {
            throw new NotFoundHttpException(
                sprintf(
                    'Impossible de trouver la prestation ayant pour code %s.',
                    $slug
                )
            );
        }

        return $appearances;
    }
}
