<?php

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

use Khatovar\Bundle\WebBundle\Helper\EntityHelper;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class PhotoHelper
{
    /**
     * Get a list of all entities that can be linked to photos.
     *
     * When adding a new entity to the list, remember to also add it to
     * PhotoRepository::getOrphans() and in the _form.html.twig view.
     *
     * @return array
     */
    public static function getPhotoEntities()
    {
        return [
            EntityHelper::HOMEPAGE_LABEL => EntityHelper::HOMEPAGE_CODE,
            EntityHelper::APPEARANCE_LABEL => EntityHelper::APPEARANCE_CODE,
            EntityHelper::EXACTION_LABEL => EntityHelper::EXACTION_CODE,
            EntityHelper::MEMBER_LABEL => EntityHelper::MEMBER_CODE,
            EntityHelper::CONTACT_LABEL => EntityHelper::CONTACT_CODE,
        ];
    }
}
