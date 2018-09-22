<?php

declare(strict_types=1);

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

namespace Khatovar\Bundle\WebBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class MemberRepository extends EntityRepository
{
    /**
     * Finds a member by its ID or throws a 404.
     *
     * @param int $id
     *
     * @throws NotFoundHttpException
     *
     * @return Member
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
     * @throws NotFoundHttpException
     *
     * @return Member
     */
    public function findBySlugOr404($slug)
    {
        $contact = $this->findOneBy(['slug' => $slug]);

        if (!$contact) {
            throw new NotFoundHttpException('Impossible de trouver le membre.');
        }

        return $contact;
    }

    /**
     * Returns the member corresponding to the logged user, if any.
     *
     * @param int $id
     *
     * @return null|Member
     */
    public function getLoggedMember($id)
    {
        return $this->findOneBy(['owner' => $id]);
    }
}
