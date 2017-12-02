<?php

/*
 * This file is part of KhatovarWeb.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Khatovar\Bundle\UserBundle\Form\Factory;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Create forms for User entity.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
interface UserFormFactoryInterface
{
    /**
     * Creates a form to create a new entity.
     *
     * @param UserInterface $item the entity to create
     * @param string        $type The form type to use with the entity
     * @param string        $url  The route used to create the entity
     *
     * @return FormInterface
     */
    public function createCreateForm(UserInterface $item, $type, $url);

    /**
     * Creates a form to edit an entity.
     *
     * @param UserInterface $item the entity to edit
     * @param string        $type The form type to use with the entity
     * @param string        $url  The route used to edit the entity
     *
     * @return FormInterface
     */
    public function createEditForm(UserInterface $item, $type, $url);

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
     * @param UserInterface[] $items The list of entities to delete
     * @param string          $url   The route used to delete the entities
     *
     * @return FormInterface[]
     */
    public function createDeleteFormViews(array $items, $url);

    /**
     * Creates a form to set user's roles.
     *
     * @param string $currentRole
     *
     * @return FormInterface
     */
    public function createSetRoleForm($currentRole);
}
