<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrdersType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('number', 'text', array('label' => 'Order#', 'disabled' => true))
//                ->add('state', 'choice', array('placeholder' => 'State'))
//                ->add('channel', 'choice', array('placeholder' => 'Channel', 'disabled' => true))
                ->add('productsTotal', 'money', array('currency' => 'RUB', 'divisor' => 100, 'label' => 'Subtotal', 'disabled' => 'true'))
                ->add('adjustmentTotal', 'money', array('currency' => 'RUB', 'divisor' => 100, 'label' => 'Adjustment'))
                ->add('total', 'money', array('currency' => 'RUB', 'divisor' => 100, 'required' => false))
//                ->add('createdAt', 'hidden')
//                ->add('updatedAt', 'hidden', array('required' => false))
//                ->add('deletedAt', 'hidden', array('required' => false))
//                ->add('completedAt', 'hidden', array('required' => false))
        ;
        $builder->add('customer', new \MeVisa\CRMBundle\Form\CustomersType());

        $builder->add('orderProducts', 'collection', array(
            'type' => new OrderProductsType(),
            'allow_add' => true));

        $builder->add('orderCompanions', 'collection', array(
            'type' => new OrderCompanionsType(),
            'allow_add' => true
        ));

        $builder->add('payments', 'collection', array(
            'type' => new OrderPaymentsType(),
            'allow_add' => true
        ));

        $builder->add('comments', 'collection', array(
            'type' => new OrderCommentsType(),
            'allow_add' => true
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
        return 'mevisa_erpbundle_orders';
    }

}
