<?php

/*
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

namespace Khatovar\Bundle\DocumentsBundle\Saver;

/**
 * Saves an object.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface SaverInterface
{
    /**
     * Saves an object and returns a message to display.
     *
     * The mandatory option is:
     *
     *  [
     *      'flush' => bool,   # True to flush the object,false to only persist it
     *  ]
     *
     * @param mixed $object  the object to save
     * @param array $options the saving options
     */
    public function save($object, array $options = []);
}
