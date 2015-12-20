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

namespace Khatovar\Bundle\WebBundle\Helper;

/**
 * Lists the codes and labels of all the different type of entities.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class EntityHelper
{
    /** @staticvar string */
    const HOMEPAGE_CODE = 'homepage';

    /** @staticvar string */
    const HOMEPAGE_LABEL = 'Pages d\'accueil';

    /** @staticvar string */
    const APPEARANCE_CODE = 'appearance';

    /** @staticvar string */
    const APPEARANCE_LABEL = 'Prestations';

    /** @staticvar string */
    const EXACTION_CODE = 'exaction';

    /** @staticvar string */
    const EXACTION_LABEL = 'Exactions';

    /** @staticvar string */
    const MEMBER_CODE = 'member';

    /** @staticvar string */
    const MEMBER_LABEL = 'Membres';

    /** @staticvar string */
    const CONTACT_CODE = 'contact';

    /** @staticvar string */
    const CONTACT_LABEL = 'Pages de contact';

    /**
     * Return the
     *
     * @return array
     */
    public static function getActivables()
    {
        return [
            EntityHelper::HOMEPAGE_CODE,
            EntityHelper::CONTACT_CODE,
        ];
    }
}
