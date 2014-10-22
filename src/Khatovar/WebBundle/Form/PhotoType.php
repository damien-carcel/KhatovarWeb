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

namespace Khatovar\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PhotoType
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Form
 */
class PhotoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (is_null($builder->getData()->getId())) {
            $builder->add('file', 'file', array('label' => false));
        }

        $builder->add('alt', 'text', array('label' => 'Nom de substitution : '))
            // TODO: Find a way to get the entities list automatically
            ->add('entity', 'choice', array(
                    'label' => 'Rattacher la photo à : ',
                    'choices' => array(
                        'homepage' => 'Page d’accueil',
                        'member' => 'Membre'
                    ),
                    'preferred_choices' => array('homepage')
                ));

        $formModifier = function (FormInterface $form, $entity) {
            // First we check if entity is defined. If not, then it is
            // a photo upload, so we don't have other fields to add to
            // the form. If it is not null, then the photo is already
            // uploaded and we are editing it.
            if (!is_null($entity)) {
                // If entity is defined as "homepage", then we have to
                // define the class to apply to the photo.
                if ($entity == 'homepage') {
                    $form->add('class', 'choice', array(
                            'label' => 'Taille de la photo : ',
                            'choices' => array(
                                'photo_small' => 'Petit format',
                                'photo' => 'Format normal',
                                'panorama' => 'Panorama'
                            ),
                            'preferred_choices' => array('photo'),
                            'required' => true
                        ))
                        ->add('entry', 'hidden', array(
                                'data' => 0
                            ));
                // For the other entities, class are not an option and
                // are defined in the templates, but we have to define
                // which entity child own the photo.
                } else {
                    $form->add('class', 'hidden', array(
                            'data' => 'none'
                        ))
                        ->add('entry', 'entity', array(
                            'class' => 'KhatovarWebBundle:' . ucfirst($entity),
                            'property' => 'name',
                            'required' => true
                        ));
                }
            }
        };

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

        $builder->add('submit', 'submit', array('label' => 'Envoyer'));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Khatovar\WebBundle\Entity\Photo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_webbundle_photo';
    }
}
