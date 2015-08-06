<?php

namespace Khatovar\Bundle\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add(
                'name',
                'text',
                array(
                    'label' => 'Titre',
                )
            )
            ->add(
                'content',
                'ckeditor',
                array(
                    'config_name' => 'basic_config',
                    'label'       => 'Contenu',
                )
            )
            ->add(
                'visitCard',
                'entity',
                array(
                    'class'    => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                    'property' => 'alt',
                    'label'    => 'Carte de visite',
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Khatovar\Bundle\ContactBundle\Entity\Contact'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'khatovar_bundle_contactbundle_contact';
    }
}
