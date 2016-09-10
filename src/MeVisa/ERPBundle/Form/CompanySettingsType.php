<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanySettingsType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name')
        ->add('address', 'textarea', array(
          "attr" => array(
            "class" => "ckeditor"
      )))
        ->add('bank', 'textarea', array(
          "attr" => array(
            "class" => "ckeditor"
      )))
        ->add('invoiceSignature1', 'textarea', array(
          "attr" => array(
            "class" => "ckeditor"
      )))
        ->add('invoiceSignature2', 'textarea', array(
          "attr" => array(
            "class" => "ckeditor"
      )))
        ->add('agreement', 'textarea', array(
          "attr" => array(
            "class" => "ckeditor"
      )))
        ->add('agreementSignature', 'textarea', array(
          "attr" => array(
            "class" => "ckeditor"
      )))
        ->add('agreementSignatureName')
        ->add('waiver', 'textarea', array(
          "attr" => array(
            "class" => "ckeditor"
      )))
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
      'data_class' => 'MeVisa\ERPBundle\Entity\CompanySettings'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'mevisa_erpbundle_companysettings';
  }

}
