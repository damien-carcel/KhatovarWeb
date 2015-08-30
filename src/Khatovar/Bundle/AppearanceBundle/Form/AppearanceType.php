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

namespace Khatovar\Bundle\AppearanceBundle\Form;

use Khatovar\Bundle\AppearanceBundle\Helper\AppearanceHelper;
use Symfony\Component\Form\AbstractType;
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
            ->add('name', 'text', ['label' => 'Nom'])
            ->add(
                'content',
                'ckeditor',
                [
                    'label'       => false,
                    'config_name' => 'basic_config',
                ]
            )->add(
                'active',
                'checkbox',
                [
                    'label'    => 'Prestation proposée',
                    'required' => false,
                ]
            )->add(
                'pageType',
                'choice',
                [
                    'label'   => 'Type de page',
                    'choices' => AppearanceHelper::getAppearancePageTypes(),
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_appearance_type';
    }
}
