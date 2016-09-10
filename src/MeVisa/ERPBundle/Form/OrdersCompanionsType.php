<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdersCompanionsType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('orderCompanions', 'collection', array(
      'type' => new OrderCompanionsType(),
      'allow_add' => true,
      'allow_delete' => true,
      'label' => false,
    ));
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
      'data_class' => 'MeVisa\ERPBundle\Entity\Orders'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'mevisa_erpbundle_orders_companions';
  }

}
