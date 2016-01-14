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
//            ->add('number', 'text',
//                array('label' => 'Order#', 'disabled' => true))
                ->add('state', 'choice', array(
                    'placeholder' => 'State',
                    'choices' => array('pending', 'backoffice'),
                ))

//                ->add('channel', 'choice', array('placeholder' => 'Channel', 'disabled' => true))
                ->add('productsTotal', 'money', array('currency' => 'RUB', 'divisor' => 100, 'label' => 'Subtotal',
                    'disabled' => 'true'))
                ->add('adjustmentTotal', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Adjustment',
                    'data' => 0))
                ->add('total', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'required' => false,
                    'disabled' => 'true'))
                ->add('people', 'integer', array(
                    'attr' => array('min' => 1)
                ))
                ->add('departure', 'date')
                ->add('arrival', 'date')
//                ->add('createdAt', 'hidden')
//                ->add('updatedAt', 'hidden', array('required' => false))
//                ->add('deletedAt', 'hidden', array('required' => false))
//                ->add('completedAt', 'hidden', array('required' => false))
        ;
        $builder->add('customer', new \MeVisa\CRMBundle\Form\CustomersType());

        $builder->add('orderProducts', 'collection', array(
            'type' => new OrderProductsType(),
            'allow_add' => true,
            'label' => false,
        ));

        $builder->add('orderCompanions', 'collection', array(
            'type' => new OrderCompanionsType(),
            'allow_add' => true,
            'label' => false
        ));

        $builder->add('orderPayments', 'collection', array(
            'type' => new OrderPaymentsType(),
            'allow_add' => true,
            'label' => false
        ));

        $builder->add('orderComments', 'collection', array(
            'type' => new OrderCommentsType(),
            'allow_add' => true,
            'label' => false
        ));
        $builder->add('orderDocuments', 'collection', array(
            'type' => new OrderDocumentsType(),
            'allow_add' => true,
            'label' => false,
            'attr' => array('class' => 'orderDocuments', "multiple" => "multiple")
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
