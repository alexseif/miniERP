<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderProductsFixType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
                ->add('productsTotal', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Subtotal',
                        )
                )
                ->add('adjustmentTotal', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Adjustment',
                    'data' => 0
                        )
                )
                ->add('total', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'required' => false,
                        )
                )
                ->add('people', 'integer', array(
                    'label' => 'PAX No',
                    'attr' => array('min' => 1)
                        )
        );

        $builder->add('orderProducts', 'collection', array(
            'type' => new OrderProductsWCostType(),
            'allow_add' => false,
            'allow_delete' => false,
            'label' => false,
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'mevisa_erpbundle_orders_products_fix';
    }

}
