<?php

namespace MeVisa\ERPBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityRepository;

class OrdersType extends AbstractType
{

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $isAccountant = $options['isAccountant'];

    $availableStates = $options['data']->getOrderState()->getAvailableStates();
    $states = array($options['data']->getOrderState()->getCurrentState()->getKey() => $options['data']->getOrderState()->getCurrentState()->getName());
    foreach ($availableStates as $state) {
      $states[$state->getKey()] = $state->getName();
    }
    $builder
        ->add('state', ChoiceType::class, array(
          'choices' => $states,
          'expanded' => true,
          'attr' => array('class' => 'align-inline')
            )
        )
        ->add('productsTotal', MoneyType::class, array(
          'currency' => 'RUB',
          'divisor' => 100,
          'label' => 'Subtotal',
          'read_only' => true
            )
        )
        ->add('adjustmentTotal', MoneyType::class, array(
          'currency' => 'RUB',
          'divisor' => 100,
          'label' => 'Adjustment',
          'data' => 0
            )
        )
        ->add('total', MoneyType::class, array(
          'currency' => 'RUB',
          'divisor' => 100,
          'required' => false,
          'read_only' => true
            )
        )
        ->add('people', IntegerType::class, array(
          'label' => 'PAX No',
          'attr' => array('min' => 1)
            )
        )
        ->add('departure', DateType::class, array(
          'widget' => 'single_text',
          'format' => 'dd.MM.yyyy',
          'attr' => array(
            'class' => 'form-control input-inline datepicker',
            'data-provide' => 'datepicker',
            'data-date-format' => 'dd.mm.yyyy',
          ))
        )
        ->add('arrival', DateType::class, array(
          'widget' => 'single_text',
          'format' => 'dd.MM.yyyy',
          'attr' => array(
            'class' => 'form-control input-inline datepicker',
            'data-provide' => 'datepicker',
            'data-date-format' => 'dd.mm.yyyy',
          ))
        )
        ->add('ticketRequired', CheckboxType::class, array(
          'required' => false,
        ))
        ->add('salesBy', EntityType::class, array(
          'placeholder' => 'Choose a User',
          'class' => 'AppBundle:User',
          'query_builder' => function (EntityRepository $em) {
            return $em->createQueryBuilder('u')
                ->where("u.enabled = true")
                ->andWhere('u.roles NOT LIKE :acc')
                ->setParameter('acc', '%"ROLE_ACCOUNTANT"%')
                ->orderBy('u.username', 'ASC');
          }
        ))
    ;
    $builder->add('customer', new \MeVisa\CRMBundle\Form\CustomersType());
    $agent = false;
    if ($options['data']->getCustomer()) {
      $agent = $options['data']->getCustomer()->getAgent();
    }
    $builder->add('orderProducts', CollectionType::class, array(
      'type' => new OrderProductsType($agent),
      'entry_options' => array(
        'isAccountant' => $options['isAccountant']
      ),
      'allow_add' => true,
      'allow_delete' => true,
      'label' => false,
    ));

    $builder->add('orderCompanions', CollectionType::class, array(
      'type' => new OrderCompanionsType(),
      'allow_add' => true,
      'allow_delete' => true,
      'label' => false,
    ));

    $builder->add('orderPayments', CollectionType::class, array(
      'type' => new OrderPaymentsType(),
      'allow_add' => true,
    ));

    $builder->add('orderComments', CollectionType::class, array(
      'type' => new OrderCommentsType(),
      'allow_add' => true,
      'allow_delete' => false,
      'delete_empty' => true,
      'required' => false,
      'label' => false
    ));

    $builder->add('uploadedFiles', FileType::class, array(
      'multiple' => true,
      'data_class' => null,
      'required' => false,
        )
    );
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $this->configureOptions($resolver);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'MeVisa\ERPBundle\Entity\Orders',
      'isAccountant' => null
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
