<?php

declare(strict_types=1);

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

namespace Khatovar\Bundle\DocumentsBundle\Form\Creator;

use Symfony\Component\Form\FormInterface;

/**
 * Creates forms for Folder and File entities.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface DocumentsFormCreatorInterface
{
    /**
     * Creates a form to create a new entity.
     *
     * @param object $item the entity to create
     * @param string $type The form type to use with the entity
     * @param string $url  The route used to create the entity
     *
     * @return FormInterface
     */
    public function createCreateForm($item, $type, $url);

    /**
     * Creates a form to edit an entity.
     *
     * @param object $item the entity to edit
     * @param string $type The form type to use with the entity
     * @param string $url  The route used to edit the entity
     *
     * @return FormInterface
     */
    public function createEditForm($item, $type, $url);

    /**
     * Creates a form to delete an entity.
     *
     * @param int    $id  The ID of the entity to delete
     * @param string $url The route used to delete the entity
     *
     * @return FormInterface
     */
    public function createDeleteForm($id, $url);

    /**
     * Return a list of delete forms for a set entities.
     *
     * @param object[] $items The list of entities to delete
     * @param string   $url   The route used to delete the entities
     *
     * @return FormInterface[]
     */
    public function createDeleteForms(array $items, $url);

    /**
     * Creates a form to move a folder or a file.
     *
     * @param object $item the file or folder to move
     * @param string $type The form type to use with the entity
     * @param string $url  The route used to edit the entity
     *
     * @return FormInterface
     */
    public function createMoveForm($item, $type, $url);
}
