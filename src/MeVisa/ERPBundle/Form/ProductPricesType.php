<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPricesType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('cost', 'money', array(
          'currency' => 'RUB',
          'divisor' => 100
        ))
        ->add('price', 'money', array(
          'currency' => 'RUB',
          'divisor' => 100
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
      'data_class' => 'MeVisa\ERPBundle\Entity\ProductPrices'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'mevisa_erpbundle_productprices';
  }

}
