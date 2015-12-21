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

namespace Khatovar\Bundle\MemberBundle\Form\Subscriber;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Adds a portrait field to the Member form only on edit action.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AddPortraitSubscriber implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    public function preSetData(FormEvent $formEvent)
    {
        $member = $formEvent->getData();
        $form   = $formEvent->getForm();

        if (null !== $member->getId()) {
            $form->add(
                'portrait',
                EntityType::class,
                [
                    'label'         => 'Photo de profil',
                    'class'         => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                    'choice_label'  => 'alt',
                    'query_builder' => function (EntityRepository $er) use ($member) {
                        return $er->createQueryBuilder('p')
                            ->where('p.member = :member')
                            ->setParameter('member', $member);
                    }
                ]
            );
        }
    }
}
