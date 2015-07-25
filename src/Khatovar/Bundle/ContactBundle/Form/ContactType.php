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
            ->add('title', 'text')
            ->add(
                'content',
                'ckeditor',
                array(
                    'label'       => false,
                    'config_name' => 'basic_config',
                )
            )
            ->add('active', 'checkbox')
            ->add(
                'visitCard',
                'entity',
                array(
                    'class'    => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                    'property' => 'alt',
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
