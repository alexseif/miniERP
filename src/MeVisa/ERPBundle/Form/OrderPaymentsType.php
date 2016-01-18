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
                ->add('state', 'choice', array(
                    // TODO: get payment state properly
                    'choices' => array(
                        "paid" => 'Paid',
                        "not_paid" => 'Not Paid'),
                    'expanded' => true,
                    'attr' => array('class' => 'align-inline')
                ))
                ->add('method', 'choice', array(
                    // TODO: get payment options properly
                    'choices' => array(
                        'online' => 'Online',
                        'banktransfer' => 'Bank Transfer',
                        'creditcard' => 'Credit Card',
                        'cash' => 'Cash'
                    ),
                    'required' => false
                ))
                ->add('amount', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'required' => false
                ))
                ->add('detail', 'textarea', array(
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
