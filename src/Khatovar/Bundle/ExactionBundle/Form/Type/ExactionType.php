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
 * @copyright Copyright (C) Damien Carcel (https://github.com/damien-carcel)
 * @link      https://github.com/damien-carcel/KhatovarWeb
 * @license   http://www.gnu.org/licenses/gpl.html
 */

namespace Khatovar\Bundle\ExactionBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Khatovar\Bundle\ExactionBundle\Entity\Exaction;
use Khatovar\Bundle\WebBundle\Helper\EntityHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Exaction form type.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ExactionType extends AbstractType
{
    /** @staticvar int */
    const KHATOVAR_CREATION = 2008;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                    'label'    => 'Annonce',
                    'required' => false,
                ]
            )
            ->add(
                'links',
                CollectionType::class,
                [
                    'label'              => 'Liens utiles',
                    'entry_type'         => TextType::class,
                    'allow_add'          => true,
                    'allow_delete'       => true,
                ]
            )
        ;

        $this->addEdtionSpecificFields($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Khatovar\Bundle\ExactionBundle\Entity\Exaction']);
    }

    /**
     * Add form fields that are available only when editing an existing
     * exaction.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addEdtionSpecificFields(FormBuilderInterface $builder)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $exaction = $event->getData();

                if ($exaction instanceof Exaction &&
                    null !== $exaction->getName() &&
                    $exaction->getStart() <= new \DateTime()
                ) {
                    $form
                        ->add(
                            'image',
                            EntityType::class,
                            [
                                'class'         => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                                'label'         => 'L\'image de la fête',
                                'required'      => false,
                                'query_builder' => function (EntityRepository $repository) use ($exaction) {
                                    return $repository
                                        ->createQueryBuilder('e')
                                        ->where('e.exaction = :exaction')
                                        ->setParameter(EntityHelper::EXACTION_CODE, $exaction);
                                }
                            ]
                        )
                        ->add(
                            'onlyPhotos',
                            CheckboxType::class,
                            [
                                'label'    => 'Pas de résumé de fête, seulement des photos ?',
                                'required' => false,
                            ]
                        )
                        ->add(
                            'abstract',
                            TextareaType::class,
                            [
                                'label'    => 'Résumé de la fête',
                                'required' => false,
                            ]
                        )
                        ->add(
                            'imageStory',
                            TextareaType::class,
                            [
                                'label'    => 'Explication de l\'image de la fête',
                                'required' => false,
                            ]
                        );
                }
            }
        );
    }

    /**
     * Return the list of years since the creation of the compagnie.
     *
     * @return array
     */
    protected function getYearslist()
    {
        $currentYear = new \DateTime();
        $startYear   = static::KHATOVAR_CREATION;
        $endYear     = ((int) $currentYear->format('Y')) + 3;
        $yearList    = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearList[] = $year;
        }

        return $yearList;
    }
}
