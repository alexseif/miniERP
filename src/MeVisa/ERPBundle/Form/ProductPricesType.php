<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductPricesType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('age', 'choice', array(
                    'choices' => array('all' => 'All',
                        'i' => 'Infant',
                        'c' => 'Child',
                        'a' => 'Adult')
                ))
                ->add('cost')
                ->add('costCurrency', 'choice', array(
                    'choices' => array(
                        'USD' => 'USD',
                        'RUB' => 'RUB',),
                    'attr' => array('class' => 'pull-right')
                ))
                ->add('price')
                ->add('priceCurrency', 'choice', array(
                    'choices' => array(
                        'USD' => 'USD',
                        'RUB' => 'RUB',)
                ))
                ->add('product', 'entity', array(
                    'class' => 'MeVisaERPBundle:Products',
                    'choice_label' => 'name',
                ))
        ;
//        'hidden', array(
//    'data' => 'abcdef',
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
