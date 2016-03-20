<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name')
                ->add('vendor', 'entity', array(
                    'class' => 'MeVisaERPBundle:Vendors',
                    'choice_label' => 'name',
                    'attr' => array('class' => 'chosen')
                ))
                ->add('enabled', 'checkbox', array(
                    'required' => false,
                    'attr' => array(
                    ),
                ))
                ->add('requiredDocuments', 'choice', array(
                    'choices' => array(
                        'passport' => 'Passport Copy',
                        'id' => 'ID Copy',
                        'card' => 'Card Copy',
                    ),
                    'multiple' => true,
                    'expanded' => true,
                    'label_attr' => array(
                    ),
                    'attr' => array(
                        'class' => 'col-sm-12'
                    )
                ))
                ->add('pricing', 'collection', array(
                    'type' => new ProductPricesType(),
                    'allow_add' => true,
                    'attr' => array(
                        'class' => 'pricing'
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
            'data_class' => 'MeVisa\ERPBundle\Entity\Products'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mevisa_erpbundle_products';
    }

}
