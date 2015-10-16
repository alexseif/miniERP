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
                ->add('state', 'choice', array('placeholder' => 'State'))
//                ->add('customer')
                ->add('channel', 'choice', array('placeholder' => 'Channel', 'disabled' => true))
                ->add('productsTotal', 'money', array('currency' => 'RUB', 'divisor' => 100, 'label' => 'Subtotal', 'disabled' => 'true'))
                ->add('adjustmentTotal', 'money', array('currency' => 'RUB', 'divisor' => 100, 'label' => 'Adjustment'))
                ->add('total', 'money', array('currency' => 'RUB', 'divisor' => 100))
                ->add('createdAt', 'date', array('disabled' => true))
//            ->add('updatedAt')
//            ->add('deletedAt')
//            ->add('completedAt')
        ;
        $builder->add('customer', 'collection', array('type' => new \MeVisa\CRMBundle\Form\CustomerType(), 'allow_add' => true));
        $builder->add('comments', 'collection', array('type' => new OrderCommentsType(), 'allow_add' => true));
        $builder->add('products', 'collection', array('type' => new OrderProductsType(), 'allow_add' => true));
        $builder->add('payments', 'collection', array('type' => new OrderPaymentsType(), 'allow_add' => true));
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
