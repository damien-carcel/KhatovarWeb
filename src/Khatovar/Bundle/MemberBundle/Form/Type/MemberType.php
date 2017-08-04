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

namespace Khatovar\Bundle\MemberBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Member form type.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class MemberType extends AbstractType
{
    /** @var EventSubscriberInterface */
    protected $addPortraitSubscriber;

    /** @var EventSubscriberInterface */
    protected $removeOwnerSubscriber;

    /**
     * @param EventSubscriberInterface $addPortraitSubscriber
     * @param EventSubscriberInterface $removeOwnerSubscriber
     */
    public function __construct(
        EventSubscriberInterface $addPortraitSubscriber,
        EventSubscriberInterface $removeOwnerSubscriber
    ) {
        $this->addPortraitSubscriber = $addPortraitSubscriber;
        $this->removeOwnerSubscriber = $removeOwnerSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addBaseFields($builder);
        $this->addDescriptionFields($builder);
        $this->addEventSubscribers($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\MemberBundle\Entity\Member']);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addBaseFields(FormBuilderInterface $builder)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add(
                'active',
                CheckboxType::class,
                [
                    'label'    => 'Membre actif',
                    'required' => false,
                ]
            )
            ->add(
                'owner',
                EntityType::class,
                [
                    'label'         => 'Utilisateur lié',
                    'class'         => 'Carcel\Bundle\UserBundle\Entity\User',
                    'choice_label'  => 'username',
                    'required'      => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('o')->orderBy('o.username');
                    },
                ]
            );
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addDescriptionFields(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                'rank',
                TextType::class,
                [
                    'label'    => 'Rang',
                    'required' => false,
                ]
            )
            ->add(
                'quote',
                TextType::class,
                [
                    'label'    => 'Citation',
                    'required' => false,
                ]
            )
            ->add(
                'skill',
                TextType::class,
                [
                    'label'    => 'Compétences',
                    'required' => false,
                ]
            )
            ->add(
                'age',
                TextType::class,
                [
                    'label'    => 'Âge',
                    'required' => false,
                ]
            )
            ->add(
                'size',
                TextType::class,
                [
                    'label'    => 'Taille',
                    'required' => false,
                ]
            )
            ->add(
                'weight',
                TextType::class,
                [
                    'label'    => 'Poids',
                    'required' => false,
                ]
            )
            ->add(
                'strength',
                TextType::class,
                [
                    'label'    => 'Point fort',
                    'required' => false,
                ]
            )
            ->add(
                'weakness',
                TextType::class,
                [
                    'label'    => 'Point faible',
                    'required' => false,
                ]
            )
            ->add('story', TextareaType::class, ['label' => 'Histoire personnelle']);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addEventSubscribers(FormBuilderInterface $builder)
    {
        $builder
            ->addEventSubscriber($this->addPortraitSubscriber)
            ->addEventSubscriber($this->removeOwnerSubscriber);
    }
}
