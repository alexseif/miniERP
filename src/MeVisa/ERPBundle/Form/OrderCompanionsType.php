<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderCompanionsType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', 'text', array(
          'attr' => array(
            'placeholder' => 'Name'
          )
        ))
        ->add('nationality', 'country', array(
          'preferred_choices' => array('RU', 'UA'),
          'data' => 'RU',
          'attr' => array('class' => 'chosen-input')
        ))
        /*
         * Russian passport format: RRYYSSSSSS (10 digits)
          R - two digits that correspond to the code assigned to the appropriate region of the Russian Federation
          Y - year of issuance of the passport form. New Russian passport numbers were introduced in 1997.
          S - six digits of the serial number
         */
        ->add('passportNumber', 'text', array(
          'label' => 'Passport #',
          'attr' => array(
            'placeholder' => 'Passport #'
          )
        ))
        ->add('passportExpiry', 'date', array(
          'widget' => 'choice',
          'years' => range(date('Y'), date('Y') + 12),
          'format' => 'ddMMyyy',
        ))
        ->add('orderRef')
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
      'data_class' => 'MeVisa\ERPBundle\Entity\OrderCompanions'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'mevisa_erpbundle_ordercompanions';
  }

}
