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

namespace Khatovar\Bundle\ExactionBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Exaction form type.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ExactionType extends AbstractType
{
    /** @var bool */
    protected $exactionExists;

    /**
     * @param bool $exactionExists
     */
    public function __construct($exactionExists = false)
    {
        $this->exactionExists = $exactionExists;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nom'))
            ->add('place', 'text', array('label' => 'Lieu'))
            ->add('start', 'date', array('label' => 'Date de début'))
            ->add('end', 'date', array('label' => 'Date de fin'))
            ->add('map', 'textarea', array('label' => 'Emplacement (copier le lien depuis Google Map)'))
            ->add('introduction', 'textarea', array('label' => 'Annonce'))
            ->add(
                'links',
                'collection',
                array(
                    'label'              => 'Liens utiles',
                    'type'               => 'text',
                    'allow_add'          => true,
                    'allow_delete'       => true,
                    'cascade_validation' => true,
                )
            )
        ;

        if ($this->exactionExists) {
            $this->addEdtionSpecificFields($builder);
        }

        $builder->add('submit', 'submit', array('label' => 'Sauvegarder'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Khatovar\Bundle\ExactionBundle\Entity\Exaction'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'khatovar_bundle_exactionbundle_exaction';
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

                if (null !== $exaction) {
                    $form->add(
                        'image',
                        'entity',
                        array(
                            'class'         => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                            'label'         => 'L\'image de la fête',
                            'required'      => false,
                            'query_builder' => function (EntityRepository $repository) use ($exaction) {
                                return $repository
                                    ->createQueryBuilder('e')
                                    ->where('e.exaction = :exaction')
                                    ->setParameter('exaction', $exaction);
                            }
                        )
                    );
                }
            }
        );

        $builder
            ->add(
                'onlyPhotos',
                'checkbox',
                array(
                    'label'    => 'Pas de résumé de fête, seulement des photos ?',
                    'required' => false
                )
            )
            ->add(
                'abstract',
                'textarea',
                array(
                    'label'    => 'Résumé de la fête',
                    'required' => false,
                )
            )
            ->add(
                'imageStory',
                'textarea',
                array(
                    'label'    => 'Explication de l\'image de la fête',
                    'required' => false,
                )
            )
        ;
    }
}
