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
                ->add('product', 'entity', array(
                    'class' => 'MeVisa\ERPBundle\Entity\Products',
                    'choice_label' => 'name',
                    'placeholder' => 'Select Product',
                    'query_builder' => function(\MeVisa\ERPBundle\Entity\ProductsRepository $pr) {
                        return $pr->queryAllEnabledProducts();
                    },
                    'attr' => array(
                        'class' => 'product_id',
                    )
                ))
                ->add('quantity', 'number')
                ->add('unitPrice', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Price',
                    'disabled' => 'true'
                ))
                ->add('total', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'disabled' => 'true'
                ))
        // TODO: Find enabled products
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
