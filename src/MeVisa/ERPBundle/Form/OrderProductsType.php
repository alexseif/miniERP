<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderProductsType extends AbstractType
{

  private $agent;

  function __construct($agent = false)
  {
    $this->agent = $agent;
  }

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $isAccountant = $options['isAccountant'];
    $builder
        ->add('product', 'entity', array(
          'class' => 'MeVisa\ERPBundle\Entity\Products',
          'group_by' => function($product) {
            return $product->getCountry();
          },
          'choice_label' => 'countryAndName',
          'placeholder' => 'Select Product',
          'query_builder' => function(\MeVisa\ERPBundle\Entity\ProductsRepository $pr) {
            return $pr->queryAllEnabled();
          },
          'attr' => array(
            'class' => 'product_id chosen',
          )
        ))
        ->add('quantity', 'integer', array(
          'label' => 'Qty',
          'attr' => array('min' => 1)
        ))
        ->add('total', 'money', array(
          'currency' => 'RUB',
          'divisor' => 100,
          'read_only' => true
        ))
        ->add('unitPrice', 'money', array(
          'currency' => 'RUB',
          'divisor' => 100,
          'read_only' => !($this->agent || $isAccountant)
        ))
        ->add('vendor', 'entity', array(
          'class' => 'MeVisaERPBundle:Vendors',
          'placeholder' => 'Select Vendor',
          'choice_label' => 'name',
          'attr' => array('class' => 'chosen')
        ))
    ;
    if ($isAccountant) {
      $builder->add('unitCost', 'money', array(
        'currency' => 'RUB',
        'divisor' => 100,
      ));
    }
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
      'data_class' => 'MeVisa\ERPBundle\Entity\OrderProducts',
      'isAccountant' => null
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'mevisa_erpbundle_orderproducts';
  }

}
