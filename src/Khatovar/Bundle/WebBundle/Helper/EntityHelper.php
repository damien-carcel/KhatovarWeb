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

namespace Khatovar\Bundle\WebBundle\Helper;

/**
 * Lists the codes and labels of all the different type of entities.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class EntityHelper
{
    /** @staticvar string */
    public const HOMEPAGE_CODE = 'homepage';

    /** @staticvar string */
    public const HOMEPAGE_LABEL = 'Pages d\'accueil';

    /** @staticvar string */
    public const APPEARANCE_CODE = 'appearance';

    /** @staticvar string */
    public const APPEARANCE_LABEL = 'Prestations';

    /** @staticvar string */
    public const EXACTION_CODE = 'exaction';

    /** @staticvar string */
    public const EXACTION_LABEL = 'Exactions';

    /** @staticvar string */
    public const MEMBER_CODE = 'member';

    /** @staticvar string */
    public const MEMBER_LABEL = 'Membres';

    /** @staticvar string */
    public const CONTACT_CODE = 'contact';

    /** @staticvar string */
    public const CONTACT_LABEL = 'Pages de contact';

    /**
     * Return the.
     *
     * @return array
     */
    public static function getActivables()
    {
        return [
            self::HOMEPAGE_CODE,
            self::CONTACT_CODE,
        ];
    }
}
