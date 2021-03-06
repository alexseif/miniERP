<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'payu' => 'PayU',
            'online' => 'Online',
            'creditcard' => 'Credit Card',
            'banktransfer' => 'Bank Transfer',
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
    ;
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $this->configureOptions($resolver);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
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
