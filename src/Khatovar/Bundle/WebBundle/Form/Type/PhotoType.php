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

namespace Khatovar\Bundle\WebBundle\Form\Type;

use Khatovar\Bundle\WebBundle\Helper\EntityHelper;
use Khatovar\Bundle\WebBundle\Helper\PhotoHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class PhotoType extends AbstractType
{
    /** @var EventSubscriberInterface */
    protected $addAuthorizedSubscriber;

    /** @var EventSubscriberInterface */
    protected $addFieldsSubscriber;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EventSubscriberInterface      $addFieldsSubscriber
     * @param EventSubscriberInterface      $addAuthorizedSubscriber
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EventSubscriberInterface $addFieldsSubscriber,
        EventSubscriberInterface $addAuthorizedSubscriber
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->addFieldsSubscriber = $addFieldsSubscriber;
        $this->addAuthorizedSubscriber = $addAuthorizedSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->authorizationChecker->isGranted('ROLE_EDITOR')) {
            $builder->add(
                'entity',
                ChoiceType::class,
                [
                    'label' => 'Rattacher la photo à une',
                    'choices' => PhotoHelper::getPhotoEntities(),
                    'preferred_choices' => [EntityHelper::HOMEPAGE_CODE],
                ]
            );

            $builder->addEventSubscriber($this->addAuthorizedSubscriber);
        }

        $builder->addEventSubscriber($this->addFieldsSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\WebBundle\Entity\Photo']);
    }
}
