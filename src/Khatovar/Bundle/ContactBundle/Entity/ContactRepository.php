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

namespace Khatovar\Bundle\ContactBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contact repository.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ContactRepository extends EntityRepository
{
    /**
     * Returns a contact page by its ID or throw a 404.
     *
     * @param int $id
     *
     * @throws NotFoundHttpException
     *
     * @return Contact
     */
    public function findByIdOr404($id)
    {
        $contact = $this->find($id);

        if (!$contact) {
            throw new NotFoundHttpException('Impossible de trouver le contact.');
        }

        return $contact;
    }

    /**
     * Returns active contact page or throw a 404.
     *
     * @throws NotFoundHttpException
     *
     * @return Contact
     */
    public function findActiveOr404()
    {
        $contact = $this->findOneBy(['active' => true]);

        if (null === $contact) {
            throw new NotFoundHttpException('There is no active Contact entity. You must activate one.');
        }

        return $contact;
    }
}
