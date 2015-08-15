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
use Khatovar\Bundle\AppearanceBundle\Helper\AppearanceHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Appearance manager.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AppearanceManager
{
    /** @var \Khatovar\Bundle\AppearanceBundle\Entity\AppearanceRepository */
    protected $appareanceRepository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->appareanceRepository = $entityManager->getRepository('KhatovarAppearanceBundle:Appearance');
    }

    /**
     * Find an appearance, alongside its alphabetically next and
     * previous active appearances. If appearance is a camp, it will
     * not have any previous and next ones.
     *
     * The 3 appearances are returned as an array, identified by the
     * keys "previous", "current", and "next". If previous and/or next
     * appearances don't exists, the array contain "null".
     *
     * @param string $slug
     *
     * @return Appearance[]
     */
    public function findWithNextAndPreviousOr404($slug)
    {
        if ($this->isPageType(AppearanceHelper::CAMP_TYPE_CODE, $slug)) {
            $appearances = $this->getCamp($slug);
        } else {
            $appearances = $this->getAppearances($slug);
        }

        return $appearances;
    }

    /**
     * Check if an appearance is a given type or not.
     *
     * @param string $pageType
     * @param string $slug
     *
     * @return bool
     *
     * @throws NotFoundHttpException
     */
    protected function isPageType($pageType, $slug)
    {
        $appearance = $this->appareanceRepository->findOneBy(array('slug' => $slug));

        if (!($appearance instanceof Appearance)) {
            throw new NotFoundHttpException(
                sprintf(
                    'Impossible de trouver la prestation ayant pour code %s.',
                    $slug
                )
            );
        }

        return $pageType === $appearance->getPageType();
    }

    /**
     * Return the camp page.
     *
     * @param string $slug
     *
     * @return array
     */
    protected function getCamp($slug)
    {
        $camp = $this->appareanceRepository->findOneBy(array('slug' => $slug));

        return array(
            'previous' => null,
            'current'  => $camp,
            'next'     => null,
        );
    }

    /**
     * Return a programme or an workshop with next and previous ones.
     *
     * @param string $slug
     *
     * @return array
     */
    protected function getAppearances($slug)
    {
        if ($this->isPageType(AppearanceHelper::PROGRAMME_TYPE_CODE, $slug)) {
            $sortedAppearances = $this->appareanceRepository->findAllProgrammesSortedBySlug();
        } else {
            $sortedAppearances = $this->appareanceRepository->findAllAppearancesSortedBySlug();
        }

        $appearances = array();

        for ($position = 0; $position < count($sortedAppearances); $position++) {
            if ($slug === $sortedAppearances[$position]->getSlug()) {
                $appearances['previous'] = $this->getPreviousActiveAppearance($sortedAppearances, $position);
                $appearances['current']  = $sortedAppearances[$position];
                $appearances['next']     = $this->getNextActiveAppearance($sortedAppearances, $position);

                break;
            }
        }

        return $appearances;
    }

    /**
     * @param Appearance[] $sortedAppearances
     * @param int          $currentPosition
     *
     * @return Appearance|null
     */
    protected function getPreviousActiveAppearance(array $sortedAppearances, $currentPosition)
    {
        for ($position = $currentPosition -1; $position >= 0; $position--) {
            if (isset($sortedAppearances[$position]) && $sortedAppearances[$position]->isActive()) {
                return $sortedAppearances[$position];
            }
        }

        return null;
    }

    /**
     * @param Appearance[] $sortedAppearances
     * @param int          $currentPosition
     *
     * @return Appearance|null
     */
    protected function getNextActiveAppearance(array $sortedAppearances, $currentPosition)
    {
        for ($position = $currentPosition +1; $position < count($sortedAppearances); $position++) {
            if (isset($sortedAppearances[$position]) && $sortedAppearances[$position]->isActive()) {
                return $sortedAppearances[$position];
            }
        }

        return null;
    }
}
