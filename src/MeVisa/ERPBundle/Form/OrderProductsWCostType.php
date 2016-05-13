<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderProductsWCostType extends AbstractType
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
                        return $pr->queryAllEnabled();
                    },
                    'attr' => array(
                        'class' => 'product_id',
                    )
                ))
                ->add('quantity', 'integer', array(
                    'attr' => array('min' => 1,
                        'max' => 6)
                ))
                ->add('total', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                ))
                ->add('unitCost', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Cost',
                ))
                ->add('unitPrice', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Price',
                ))
                ->add('vendor', 'entity', array(
                    'class' => 'MeVisaERPBundle:Vendors',
                    'choice_label' => 'name',
                    'attr' => array('class' => 'chosen')
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
        return 'mevisa_erpbundle_orderproducts_with_cost';
    }

}
