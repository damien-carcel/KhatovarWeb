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

namespace Khatovar\Bundle\MemberBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Member repository.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class MemberRepository extends EntityRepository
{
    /**
     * Finds a member by its ID or throws a 404.
     *
     * @param int $id
     *
     * @return Member
     *
     * @throws NotFoundHttpException
     */
    public function findByIdOr404($id)
    {
        $contact = $this->find($id);

        if (!$contact) {
            throw new NotFoundHttpException('Impossible de trouver le membre.');
        }

        return $contact;
    }

    /**
     * Finds a member by its slug or throws a 404.
     *
     * @param string $slug
     *
     * @return Member
     *
     * @throws NotFoundHttpException
     */
    public function findBySlugOr404($slug)
    {
        $contact = $this->findOneBy(['slug' => $slug]);

        if (!$contact) {
            throw new NotFoundHttpException('Impossible de trouver le membre.');
        }

        return $contact;
    }
}
