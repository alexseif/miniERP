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
                ->add('requiredDocuments', 'choice', array(
                    'choices' => array(
                        'passport' => 'Passport Copy',
                        'id' => 'ID Copy',
                        'card' => 'Card Copy',
                    ),
                    'multiple' => true,
                    'expanded' => true,
                ))
                ->add('enabled', 'checkbox', array(
                    'required' => false,
                ))
                ->add('vendor', 'entity', array(
                    'class' => 'MeVisaERPBundle:Vendors',
                    'choice_label' => 'name'
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
