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
use Khatovar\Bundle\WebBundle\Entity\Exaction;
use Khatovar\Bundle\WebBundle\Helper\EntityHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Adds fields to exaction form that need to be visible only if the
 * exaction is passed.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AddPassedExactionFieldsSubscriber implements EventSubscriberInterface
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
        $exaction = $event->getData();

        if ($exaction instanceof Exaction &&
            null !== $exaction->getName() &&
            $exaction->getStart() <= new \DateTime()
        ) {
            $form
                ->add(
                    'image',
                    EntityType::class,
                    [
                        'class' => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                        'label' => 'L\'image de la fête',
                        'required' => false,
                        'query_builder' => function (EntityRepository $repository) use ($exaction) {
                            return $repository
                                ->createQueryBuilder('e')
                                ->where('e.exaction = :exaction')
                                ->setParameter(EntityHelper::EXACTION_CODE, $exaction);
                        },
                    ]
                )
                ->add(
                    'onlyPhotos',
                    CheckboxType::class,
                    [
                        'label' => 'Pas de résumé de fête, seulement des photos ?',
                        'required' => false,
                    ]
                )
                ->add(
                    'abstract',
                    TextareaType::class,
                    [
                        'label' => 'Résumé de la fête',
                        'required' => false,
                    ]
                )
                ->add(
                    'imageStory',
                    TextareaType::class,
                    [
                        'label' => 'Explication de l\'image de la fête',
                        'required' => false,
                    ]
                );
        }
    }
}
