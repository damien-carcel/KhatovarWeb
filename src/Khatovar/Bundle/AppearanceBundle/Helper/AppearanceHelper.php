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

namespace Khatovar\Bundle\AppearanceBundle\Helper;

/**
 * Helper for choice fields of Appearance entity.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AppearanceHelper
{
    /** @staticvar string */
    const WORKSHOP_TYPE_CODE = 'appearance';

    /** @staticvar string */
    const WORKSHOP_TYPE_LABEL = 'Atelier';

    /** @staticvar string */
    const PROGRAMME_TYPE_CODE = 'programme';

    /** @staticvar string */
    const PROGRAMME_TYPE_LABEL = 'Programme';

    /** @staticvar string */
    const CAMP_TYPE_CODE = 'camp';

    /** @staticvar string */
    const CAMP_TYPE_LABEL = 'Description du campement';

    /** @staticvar string */
    const INTRO_TYPE_CODE = 'introduction';

    /** @staticvar string */
    const Intro_TYPE_LABEL = 'Introduction à nos activités';

    /**
     * @return array
     */
    public static function getAppearancePageTypes()
    {
        return [
            static::WORKSHOP_TYPE_CODE  => static::WORKSHOP_TYPE_LABEL,
            static::PROGRAMME_TYPE_CODE => static::PROGRAMME_TYPE_LABEL,
            static::CAMP_TYPE_CODE      => static::CAMP_TYPE_LABEL,
            static::INTRO_TYPE_CODE      => static::Intro_TYPE_LABEL,
        ];
    }
}
