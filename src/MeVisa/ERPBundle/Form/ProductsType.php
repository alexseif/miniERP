<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductsType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('country')
        ->add('name')
        ->add('enabled', 'checkbox', array(
          'required' => false,
        ))
        ->add('urgent', 'checkbox', array(
          'required' => false,
        ))
        ->add('wcCalc', 'checkbox', array(
          'required' => false,
        ))
        ->add('requiredDocuments', 'choice', array(
          'choices' => array(
            'passport' => 'Passport Copy',
            'id' => 'ID Copy',
            'card' => 'Card Copy',
          ),
          'multiple' => true,
          'expanded' => true,
          'label_attr' => array(
          ),
          'attr' => array(
            'class' => 'col-sm-12'
          )
        ))
//        ->add('pricing', 'collection', array(
//          'type' => new ProductPricesType(),
//          'allow_add' => true,
//          'attr' => array(
//            'class' => 'pricing'
//          )
//        ))
        ->add('vendors', EntityType::class
            , array(
          'class' => 'MeVisaERPBundle:Vendors',
          'expanded' => true,
          'multiple' => true
            )
        )
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
      'data_class' => 'MeVisa\ERPBundle\Entity\Products'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'mevisa_erpbundle_products';
  }

}
