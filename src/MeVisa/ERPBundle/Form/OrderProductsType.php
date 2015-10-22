<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class OrderProductsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('quantity')
                ->add('unitPrice', 'money', array('currency' => 'RUB', 'divisor' => 100, 'label' => 'Subtotal', 'disabled' => 'true'))
                ->add('total', 'money', array('currency' => 'RUB', 'divisor' => 100, 'label' => 'Subtotal', 'disabled' => 'true'))
                // TODO: Find enabled products
                ->add('product', 'entity', array(
                    'class' => 'MeVisaERPBundle:Products',
                    'choice_label' => 'name',
                    'placeholder' => 'Select Product',
                    'attr' => array(
                        'class' => 'product_id'
                    )
                ))
                ->add('productPrice', 'entity', array(
                    'class' => 'MeVisaERPBundle:ProductPrices',
                    'choice_label' => 'age',
                    'placeholder' => 'Select Price',
                    'attr' => array(
                        'class' => 'product_price_id'
                    )
                ))

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
