<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderPaymentsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('method', 'choice',
                array(
                // TODO: get payment options properly
                'choices' => array('Online', 'Bank Transfer', 'Credit Card', 'Cash'),
                'expanded' => true
            ))
            ->add('amount', 'money',
                array(
                'currency' => 'RUB',
                'divisor' => 100,
            ))
            ->add('state', 'choice',
                array(
                // TODO: get payment state properly
                'choices' => array('Paid', 'Not Paid'),
                'placeholder' => 'State',
            ))
            ->add('detail', 'textarea',
                array(
                'required' => false
            ))
//                ->add('createdAt')
//                ->add('orderRef')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MeVisa\ERPBundle\Entity\OrderPayments'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mevisa_erpbundle_orderpayments';
    }
}