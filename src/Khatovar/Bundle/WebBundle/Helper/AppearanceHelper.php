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
 * Helper for choice fields of Appearance entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AppearanceHelper
{
    /** @staticvar string */
    public const WORKSHOP_TYPE_CODE = 'appearance';

    /** @staticvar string */
    public const WORKSHOP_TYPE_LABEL = 'Atelier';

    /** @staticvar string */
    public const PROGRAMME_TYPE_CODE = 'programme';

    /** @staticvar string */
    public const PROGRAMME_TYPE_LABEL = 'Programme';

    /** @staticvar string */
    public const CAMP_TYPE_CODE = 'camp';

    /** @staticvar string */
    public const CAMP_TYPE_LABEL = 'Description du campement';

    /** @staticvar string */
    public const INTRO_TYPE_CODE = 'introduction';

    /** @staticvar string */
    public const INTRO_TYPE_LABEL = 'Introduction à nos activités';

    /**
     * @return array
     */
    public static function getAppearancePageTypes()
    {
        return [
            static::WORKSHOP_TYPE_CODE => static::WORKSHOP_TYPE_LABEL,
            static::PROGRAMME_TYPE_CODE => static::PROGRAMME_TYPE_LABEL,
            static::CAMP_TYPE_CODE => static::CAMP_TYPE_LABEL,
            static::INTRO_TYPE_CODE => static::INTRO_TYPE_LABEL,
        ];
    }
}
