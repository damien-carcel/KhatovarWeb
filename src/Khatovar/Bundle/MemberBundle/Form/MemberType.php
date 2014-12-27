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

namespace Khatovar\Bundle\MemberBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MemberType
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\Bundle\MemberBundle\Form
 */
class MemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nom :'))
            ->add('rank', 'text', array('label' => 'Rang :'))
            ->add('quote', 'text', array('label' => 'Citation :'))
            ->add('skill', 'text', array('label' => 'Compétences :'))
            ->add('age', 'text', array('label' => 'Âge :'))
            ->add('size', 'text', array('label' => 'Taille :'))
            ->add('weight', 'text', array('label' => 'Poids :'))
            ->add('strength', 'text', array('label' => 'Point fort :'))
            ->add('weakness', 'text', array('label' => 'Point faible :'))
            ->add('story', 'textarea', array('label' => 'Histoire personnelle :'))
            ->add('active', 'checkbox', array('label' => 'Membre actif :', 'required' => false))
            ->add('owner', 'entity', array(
                    'label' => 'Utilisateur lié :',
                    'class' => 'Carcel\UserBundle\Entity\User',
                    'property' => 'username',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('o')
                            ->orderBy('o.username');
                    }
                ))
            ->add('submit', 'submit', array('label' => 'Sauvegarder'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Khatovar\Bundle\MemberBundle\Entity\Member'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'khatovar_webbundle_member';
    }
}
