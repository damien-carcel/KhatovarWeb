<?php
/**
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
 *
 * @see        https://github.com/damien-carcel/KhatovarWeb
 *
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\HomepageBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Homepage repository.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class HomepageRepository extends EntityRepository
{
    /**
     * Finds a homepage by its ID or throws a 404.
     *
     * @param int $id
     *
     * @throws NotFoundHttpException
     *
     * @return Homepage
     */
    public function findByIdOr404($id)
    {
        $homepage = $this->find($id);

        if (!$homepage) {
            throw new NotFoundHttpException('Impossible de trouver la page d\'accueil.');
        }

        return $homepage;
    }

    /**
     * Finds the active homepage or throws a 404.
     *
     * @throws NotFoundHttpException
     *
     * @return Homepage
     */
    public function findActiveOr404()
    {
        $homepage = $this->findOneBy(['active' => true]);

        if (null === $homepage) {
            throw new NotFoundHttpException('Il n\'y a pas de page de contact active. Veuillez en activer une.');
        }

        return $homepage;
    }
}
