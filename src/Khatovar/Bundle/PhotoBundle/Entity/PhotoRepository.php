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

namespace Khatovar\Bundle\PhotoBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Khatovar\Bundle\MemberBundle\Entity\Member;

/**
 * Photo repository.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class PhotoRepository extends EntityRepository
{
    /**
     * Return photos ordered by entity and entry to ease the
     * there display when an editor list all photos.
     *
     * @return Photo[]
     */
    public function getOrphans()
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.homepage IS NULL')
            ->andWhere('p.member IS NULL')
            ->andWhere('p.exaction IS NULL')
            ->andWhere('p.contact IS NULL')
            ->andWhere('p.appearance IS NULL')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Return a list of all the photos of a member, except its portrait.
     *
     * @param Member $member
     *
     * @return Photo[]
     */
    public function getAllButPortrait(Member $member)
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.member = :member')
            ->andWhere('p.id != :portrait')
            ->setParameters(
                [
                    'member'   => $member,
                    'portrait' => $member->getPortrait(),
                ]
            )
            ->getQuery();

        return $query->getResult();
    }
}
