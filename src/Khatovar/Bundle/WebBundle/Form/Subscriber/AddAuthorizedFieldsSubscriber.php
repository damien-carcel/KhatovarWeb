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

use Khatovar\Bundle\WebBundle\Entity\Photo;
use Khatovar\Bundle\WebBundle\Helper\EntityHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Adds the fields corresponding to the entity the photo is attached to.
 *
 * The "class" is only visible if the photo is attached to a homepage.
 * The correct entity field is chosen depending on the entity the photo
 * is attached to: for instance "appearance" field for and Appearance
 * entity. The codes are provided by the EntityHelper class.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AddAuthorizedFieldsSubscriber implements EventSubscriberInterface
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
        $photo = $event->getData();

        if ($photo instanceof Photo && null !== $entityName = $photo->getEntity()) {
            if (EntityHelper::HOMEPAGE_CODE === $entityName) {
                $form->add(
                    'class',
                    ChoiceType::class,
                    [
                        'label' => 'Taille de la photo',
                        'choices' => [
                            'Petit format' => 'photo_small',
                            'Format normal' => 'photo',
                            'Panorama' => 'panorama',
                        ],
                        'preferred_choices' => ['photo'],
                        'required' => true,
                    ]
                );
            } else {
                $form->add('class', HiddenType::class, ['data' => 'none']);
            }

            $form->add(
                $entityName,
                EntityType::class,
                [
                    'class' => 'Khatovar'.ucfirst($entityName).'Bundle:'.ucfirst($entityName),
                    'choice_label' => EntityHelper::EXACTION_CODE === $entityName ? 'completeName' : 'name',
                    'label' => 'Page',
                ]
            );
        }
    }
}
