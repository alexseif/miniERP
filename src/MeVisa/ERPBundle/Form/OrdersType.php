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
                ->add('state', 'choice', array(
                    'placeholder' => 'State',
                    'choices' => array(
                        'pending' => 'Pending',
                        'backoffice' => 'Back Office'),
                ))
                ->add('productsTotal', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Subtotal',
                    'read_only' => true))
                ->add('adjustmentTotal', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'label' => 'Adjustment',
                    'data' => 0))
                ->add('total', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100,
                    'required' => false,
                    'read_only' => true))
                ->add('people', 'number', array(
                    'attr' => array('min' => 1)
                ))
                ->add('departure', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy',
                    'attr' => array(
                        'class' => 'form-control input-inline datepicker',
                        'data-provide' => 'datepicker',
                        'data-date-format' => 'dd.mm.yy',
                    )
                ))
                ->add('arrival', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd.MM.yyyy',
                    'attr' => array(
                        'class' => 'form-control input-inline datepicker',
                        'data-provide' => 'datepicker',
                        'data-date-format' => 'dd.mm.yy',
            )))
//                ->add('createdAt', 'hidden')
//                ->add('updatedAt', 'hidden', array('required' => false))
//                ->add('deletedAt', 'hidden', array('required' => false))
//                ->add('completedAt', 'hidden', array('required' => false))
        ;
        $builder->add('customer', new \MeVisa\CRMBundle\Form\CustomersType());

        $builder->add('orderProducts', 'collection', array(
            'type' => new OrderProductsType(),
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
        ));

        $builder->add('orderCompanions', 'collection', array(
            'type' => new OrderCompanionsType(),
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false
        ));

        $builder->add('orderPayments', 'collection', array(
            'type' => new OrderPaymentsType(),
            'allow_add' => true,
        ));

        $builder->add('orderComments', 'collection', array(
            'type' => new OrderCommentsType(),
            'allow_add' => true,
            'allow_delete' => false,
            'label' => false
        ));

        $builder->add('uploadedFiles', 'file', array(
            'multiple' => true,
            'data_class' => null,
            'required' => false,
                )
        );
        $builder->add('invoices', 'collection', array(
            'type' => new InvoicesType(),
            'allow_add' => true,
            'label' => false
                )
        );
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
