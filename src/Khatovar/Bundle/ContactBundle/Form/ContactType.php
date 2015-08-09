<?php

namespace Khatovar\Bundle\ContactBundle\Form;

use Doctrine\ORM\EntityRepository;
use Khatovar\Bundle\ContactBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $contact = $event->getData();

                if ($contact instanceof Contact) {
                    $form
                        ->add(
                            'visitCard',
                            'entity',
                            array(
                                'class' => 'Khatovar\Bundle\PhotoBundle\Entity\Photo',
                                'property' => 'alt',
                                'label' => 'Carte de visite',
                                'required' => false,
                                'query_builder' => function (
                                    EntityRepository $repository
                                ) use ($contact) {
                                    return $repository
                                        ->createQueryBuilder('c')
                                        ->where('c.contact = :contact')
                                        ->setParameter('contact', $contact);
                                }
                            )
                        );
                }
            }
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
        return 'khatovar_contact_type';
    }
}
