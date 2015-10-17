<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderProductsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('quantity', 'number')
                ->add('unitPrice', 'money', array('currency' => 'RUB', 'divisor' => 100))
                ->add('total', 'money', array('currency' => 'RUB', 'divisor' => 100))
//                ->add('orderRef')
//                ->add('product', 'choice', array('type' => new ProductsType()))
                ->add('product', 'choice', array('placeholder' => 'Product'))
                ->add('PAX', 'choice', array('placeholder' => 'PAX'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MeVisa\ERPBundle\Entity\OrderProducts'
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
