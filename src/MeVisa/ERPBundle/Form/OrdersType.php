<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                ->add('total', 'money', array('currency' => 'RUB', 'divisor' => 100, 'required' => false))
                ->add('createdAt', 'date', array('disabled' => true))
//            ->add('updatedAt')
//            ->add('deletedAt')
//            ->add('completedAt')
        ;
        $builder->add('customer', 'entity', array(
            'class' => 'MeVisaCRMBundle:Customer',
            'choice_label' => 'name',
            'placeholder' => 'Select Customer',
            'invalid_message' => 'Please select customer or add new',
            'attr' => array(
                'class' => 'chosen-input',
                'data-placeholder' => 'Select customer'
            )
        ));
//        $builder->add('customers', 'choice', array('choices' => 'MeVisaCRMBundle:Customer', 'choice_label' => 'name', 'data_class' => 'MeVisa\CRMBundle\Entity\Customer'));
//        $builder->add('customer', 'collection', array(
//            'type' => new \MeVisa\CRMBundle\Form\CustomerType(),
//            'allow_add' => 'true'));

        $builder->add('products', 'collection', array(
            'type' => new OrderProductsType(),
            'allow_add' => true));

        $builder->addEventListener(
                FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();

            // this would be your entity
            $data = $event->getData();

            $product = $data->getProduct();
            // TODO: I think load product prices
            $positions = null === $sport ? array() : $sport->getAvailablePositions();
            $form->add('PAX', 'entity', array(
                'class' => 'MeVisaERPBundle:ProductPrices',
                'placeholder' => '',
                'choices' => $productPrices,
            ));
        }
        );

//        TODO: Add Order Companions
//        $builder->add('comments', 'collection', array('type' => new OrderCommentsType(), 'allow_add' => true));
//        $builder->add('payments', 'collection', array('type' => new OrderPaymentsType(), 'allow_add' => true));
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
