<?php

declare(strict_types=1);

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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class ExactionType extends AbstractType
{
    /** @staticvar int */
    public const KHATOVAR_CREATION = 2008;

    /** @var EventSubscriberInterface */
    protected $addPassedFields;

    /**
     * @param EventSubscriberInterface $addPassedFields
     */
    public function __construct(EventSubscriberInterface $addPassedFields)
    {
        $this->addPassedFields = $addPassedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('place', TextType::class, ['label' => 'Lieu'])
            ->add(
                'start',
                DateType::class,
                [
                    'label' => 'Date de début',
                    'years' => $this->getYearslist(),
                ]
            )
            ->add(
                'end',
                DateType::class,
                [
                    'label' => 'Date de fin',
                    'years' => $this->getYearslist(),
                ]
            )
            ->add(
                'map',
                TextareaType::class,
                [
                    'label' => 'Emplacement (copier le lien depuis Google Map)',
                    'required' => false,
                ]
            )
            ->add(
                'introduction',
                TextareaType::class,
                [
                    'label' => 'Annonce',
                    'required' => false,
                ]
            )
            ->add(
                'links',
                CollectionType::class,
                [
                    'label' => 'Liens utiles',
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );

        $builder->addEventSubscriber($this->addPassedFields);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\WebBundle\Entity\Exaction']);
    }

    /**
     * Return the list of years since the creation of the compagnie.
     *
     * @return array
     */
    protected function getYearslist()
    {
        $currentYear = new \DateTime();
        $startYear = static::KHATOVAR_CREATION;
        $endYear = ((int) $currentYear->format('Y')) + 3;
        $yearList = [];

        for ($year = $startYear; $year <= $endYear; ++$year) {
            $yearList[] = $year;
        }

        return $yearList;
    }
}
