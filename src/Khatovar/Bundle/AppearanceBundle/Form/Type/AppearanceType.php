<?php
/**
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
 *
 * @see        https://github.com/damien-carcel/KhatovarWeb
 *
 * @license     http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\AppearanceBundle\Form\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Khatovar\Bundle\AppearanceBundle\Helper\AppearanceHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Appearance form type.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class AppearanceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add(
                'content',
                CKEditorType::class,
                [
                    'label'       => false,
                    'config_name' => 'basic_config',
                ]
            )
            ->add(
                'active',
                CheckboxType::class,
                [
                    'label'    => 'Prestation proposée',
                    'required' => false,
                ]
            )
            ->add(
                'pageType',
                ChoiceType::class,
                [
                    'label'   => 'Type de page',
                    'choices' => array_flip(AppearanceHelper::getAppearancePageTypes()),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\AppearanceBundle\Entity\Appearance']);
    }
}
