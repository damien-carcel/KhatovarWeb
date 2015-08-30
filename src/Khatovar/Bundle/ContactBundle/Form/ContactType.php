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

namespace Khatovar\Bundle\ContactBundle\Form;

use Doctrine\ORM\EntityRepository;
use Khatovar\Bundle\ContactBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Contact form type.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', ['label' => 'Titre'])
            ->add(
                'content',
                'ckeditor',
                [
                    'config_name' => 'basic_config',
                    'label'       => 'Contenu',
                ]
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $contact = $event->getData();

                if ($contact instanceof Contact) {
                    $form
                        ->add(
                            'visitCard',
                            'entity',
                            [
                                'class' => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                                'property' => 'alt',
                                'label' => 'Carte de visite',
                                'required' => false,
                                'query_builder' => function (
                                    EntityRepository $repository
                                ) use ($contact) {
                                    return $repository
                                        ->createQueryBuilder('c')
                                        ->where('c.contact = :contact')
                                        ->setParameter('contact', $contact);
                                }
                            ]
                        );
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\ContactBundle\Entity\Contact']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_contact_type';
    }
}
