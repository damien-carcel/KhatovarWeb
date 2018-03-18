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

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class HomepageActivationType extends AbstractType
{
    /** @var RegistryInterface */
    protected $doctrine;

    /**
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $previousActive = $this->getPreviousActiveContact();

        $builder
            ->add(
                'active',
                EntityType::class,
                [
                    'class' => 'Khatovar\Bundle\WebBundle\Entity\Homepage',
                    'label' => false,
                    'choice_label' => 'name',
                    'preferred_choices' => [$previousActive],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Activer'])
            ->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Khatovar\Bundle\WebBundle\Entity\Homepage',
            'validation_groups' => false,
        ]);
    }

    /**
     * @return \Khatovar\Bundle\WebBundle\Entity\Contact
     */
    protected function getPreviousActiveContact()
    {
        return $this->doctrine->getRepository('KhatovarWebBundle:Homepage')->findOneBy(['active' => true]);
    }
}
