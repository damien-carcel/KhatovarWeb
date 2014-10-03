<?php

namespace Khatovar\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PhotoType
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 * @package Khatovar\WebBundle\Form
 */
class PhotoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alt', 'text', array('label' => 'Nom de substitution : '))
            ->add('size', 'choice', array('label' => 'Taille de la photo : '))
            ->add('entity', 'choice', array('label' => 'Rattacher la photo à : '))
            ->add('submit', 'submit', array('label' => 'Envoyer'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Khatovar\WebBundle\Entity\Photo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'khatovar_webbundle_photo';
    }
}
