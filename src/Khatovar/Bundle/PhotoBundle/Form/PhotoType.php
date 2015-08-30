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

namespace Khatovar\Bundle\PhotoBundle\Form;

use Khatovar\Bundle\PhotoBundle\Helper\PhotoHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Form type for the Photo entity.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\PhotoBundle\Form
 */
class PhotoType extends AbstractType
{
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addDefaultFields($builder);

        $this->addEntityField($builder);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\PhotoBundle\Entity\Photo']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'khatovar_photo_type';
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addDefaultFields(FormBuilderInterface $builder)
    {
        if (is_null($builder->getData()->getId())) {
            $builder->add('file', 'file', ['label' => false]);
        }

        $builder->add('alt', 'text', ['label' => 'Nom de substitution']);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addEntityField(FormBuilderInterface $builder)
    {
        $formModifier = function (FormInterface $form, $entity) {
            if (null !== $entity) {
                if ($entity === 'homepage') {
                    $form->add(
                        'class',
                        'choice',
                        [
                            'label'             => 'Taille de la photo',
                            'choices'           => [
                                'photo_small' => 'Petit format',
                                'photo'       => 'Format normal',
                                'panorama'    => 'Panorama',
                            ],
                            'preferred_choices' => ['photo'],
                            'required'          => true,
                        ]
                    );
                } else {
                    $form->add('class', 'hidden', ['data' => 'none']);
                }

                $form->add(
                    $entity,
                    'entity',
                    [
                        'class'    => 'Khatovar' . ucfirst($entity) . 'Bundle:' . ucfirst($entity),
                        'property' => 'name',
                        'label'    => 'Page',
                    ]
                );
            }
        };

        if ($this->authorizationChecker->isGranted('ROLE_EDITOR')) {
            $builder->add(
                'entity',
                'choice',
                [
                    'label' => 'Rattacher la photo à une',
                    'choices' => PhotoHelper::getPhotoEntities(),
                    'preferred_choices' => ['homepage'],
                ]
            );

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier) {
                    $data = $event->getData();
                    $formModifier($event->getForm(), $data->getEntity());
                }
            );

            $builder->get('entity')->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) use ($formModifier) {
                    $entity = $event->getForm()->getData();
                    $formModifier($event->getForm()->getParent(), $entity);
                }
            );
        }
    }
}
