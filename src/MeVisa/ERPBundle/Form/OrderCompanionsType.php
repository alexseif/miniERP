<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderCompanionsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('pax', 'choice', array(
                    'choices' => array('all' => 'All',
                        'i' => 'Infant',
                        'c' => 'Child',
                        'a' => 'Adult')
                ))
                ->add('nationality', 'country', array(
                    'preferred_choices' => array('RU', 'UA'),
                    'data' => 'RU',
                    'attr' => array('class' => 'chosen-input')
                ))
                ->add('passportNumber', 'text', array('label' => 'Passport #'))
                ->add('passportExpiry', 'date', array(
                    'label' => 'Expiry',
                    'format' => 'dMMMyyyy',
                    'years' => range(date('Y'), date('Y') + 12),
                    'days' => array(1),
                    'data' => new \DateTime('now'),
                    'placeholder' => array('day' => false)
                ))->add('orderRef')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MeVisa\ERPBundle\Entity\OrderCompanions'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mevisa_erpbundle_ordercompanions';
    }

}
