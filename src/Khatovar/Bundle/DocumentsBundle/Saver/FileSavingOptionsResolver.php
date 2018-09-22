<?php

declare(strict_types=1);

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
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

namespace Khatovar\Bundle\DocumentsBundle\Saver;

/**
 * Resolves saving options for a File entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class FileSavingOptionsResolver extends BaseSavingOptionsResolver
{
    /**
     * {@inheritdoc}
     *
     * Adds the folder option, specific to the File entity.
     */
    protected function createOptionsResolver()
    {
        $resolver = parent::createOptionsResolver();
        $resolver->setDefined(['folder']);

        return $resolver;
    }
}
