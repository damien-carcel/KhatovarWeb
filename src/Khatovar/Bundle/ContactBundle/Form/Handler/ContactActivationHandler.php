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

namespace Khatovar\Bundle\ContactBundle\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Khatovar\Bundle\ContactBundle\Entity\Contact;

/**
 * Handles the activation of a contact page and deactivation of the previous one.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ContactActivationHandler
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Contact $newActiveContact
     */
    public function handle(Contact $newActiveContact)
    {
        $repository = $this->entityManager->getRepository('KhatovarContactBundle:Contact');
        $oldContact = $repository->findOneBy(['active' => true]);

        if (null !== $oldContact) {
            $oldContact->setActive(false);
            $this->entityManager->persist($oldContact);
        }

        $newActiveContact->setActive(true);
        $this->entityManager->persist($newActiveContact);
        $this->entityManager->flush();
    }
}
