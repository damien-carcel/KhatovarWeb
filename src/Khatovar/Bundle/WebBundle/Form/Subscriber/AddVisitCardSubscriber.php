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

namespace Khatovar\Bundle\WebBundle\Form\Subscriber;

use Doctrine\ORM\EntityRepository;
use Khatovar\Bundle\WebBundle\Entity\Contact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Adds the contact "visitCard" field only if editing an existing contact page.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AddVisitCardSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $contact = $event->getData();

        if ($contact instanceof Contact && null !== $contact->getId()) {
            $form->add(
                'visitCard',
                EntityType::class,
                [
                    'label' => 'Carte de visite',
                    'class' => 'Khatovar\Bundle\WebBundle\Entity\Photo',
                    'choice_label' => 'alt',
                    'query_builder' => function (EntityRepository $repository) use ($contact) {
                        return $repository->createQueryBuilder('c')
                            ->where('c.contact = :contact')
                            ->setParameter('contact', $contact);
                    },
                ]
            );
        }
    }
}
