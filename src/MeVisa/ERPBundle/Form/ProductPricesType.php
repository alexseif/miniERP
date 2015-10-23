<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductPricesType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cost', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100
                ))
                ->add('price', 'money', array(
                    'currency' => 'RUB',
                    'divisor' => 100
                ))
                ->add('product', 'entity', array(
                    'class' => 'MeVisaERPBundle:Products',
                    'choice_label' => 'name',
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MeVisa\ERPBundle\Entity\ProductPrices'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'mevisa_erpbundle_productprices';
    }

}
