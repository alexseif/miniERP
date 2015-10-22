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
                ->add('phone', 'text')
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
                    'placeholder' => array('day' => false)
                ))
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
