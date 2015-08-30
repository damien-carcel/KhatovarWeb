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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Member form type.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class MemberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', ['label' => 'Nom'])
            ->add(
                'rank',
                'text',
                [
                    'label'    => 'Rang',
                    'required' => false,
                ]
            )
            ->add(
                'quote',
                'text',
                [
                    'label'    => 'Citation',
                    'required' => false,
                ]
            )
            ->add(
                'skill',
                'text',
                [
                    'label'    => 'Compétences',
                    'required' => false,
                ]
            )
            ->add(
                'age',
                'text',
                [
                    'label'    => 'Âge',
                    'required' => false,
                ]
            )
            ->add(
                'size',
                'text',
                [
                    'label'    => 'Taille',
                    'required' => false,
                ]
            )
            ->add(
                'weight',
                'text',
                [
                    'label'    => 'Poids',
                    'required' => false,
                ]
            )
            ->add(
                'strength',
                'text',
                [
                    'label'    => 'Point fort',
                    'required' => false,
                ]
            )
            ->add(
                'weakness',
                'text',
                [
                    'label'    => 'Point faible',
                    'required' => false,
                ]
            )
            ->add('story', 'textarea', ['label' => 'Histoire personnelle'])
            ->add(
                'active',
                'checkbox',
                [
                    'label'    => 'Membre actif',
                    'required' => false,
                ]
            )
            ->add(
                'owner',
                'entity',
                [
                    'label'         => 'Utilisateur lié',
                    'class'         => 'Carcel\UserBundle\Entity\User',
                    'property'      => 'username',
                    'required'      => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('o')->orderBy('o.username');
                    },
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\MemberBundle\Entity\Member']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_member_type';
    }
}
