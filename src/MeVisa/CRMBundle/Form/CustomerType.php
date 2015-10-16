<?php

namespace MeVisa\CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('email', 'email')
                ->add('phone', 'number')
                ->add('nationality', 'country', array(
                    'preferred_choices' => array('RU', 'UA'),
                    'data' => 'RU',
                ))
                ->add('passportNumber', 'text', array('label'=>'Passport#'))
                ->add('passportExpiry', 'text', array('label'=>'Passport Exp'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MeVisa\CRMBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mevisa_crmbundle_customer';
    }

}
