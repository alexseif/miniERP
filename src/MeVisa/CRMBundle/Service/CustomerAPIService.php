<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace MeVisa\CRMBundle\Service;

use Doctrine\ORM\EntityManager;
use MeVisa\CRMBundle\Entity\Customers;

class CustomerAPIService
{

  /**
   *
   * @var EntityManager 
   */
  protected $em;

  /**
   *
   * @var \Doctrine\ORM\EntityRepository 
   */
  protected $repo;

  /**
   * 
   * @param EntityManager $em
   */
  public function __construct(EntityManager $em)
  {
    $this->em = $em;
    $this->repo = $this->em->getRepository('MeVisaCRMBundle:Customers');
  }

  /**
   * 
   * @param string $name
   * @param string $email
   * @param string $phone
   * @param boolean $isAgent
   * @return Customers
   */
  public function newCustomer($name, $email, $phone, $isAgent = false)
  {
    $customerExists = $this->checkCustomerExistsByEmail($email);
    if (!$customerExists) {
      $customer = new Customers();
      $customer->setName($name);
      $customer->setEmail($email);
      $customer->setPhone($phone);
      $customer->setAgent($isAgent);
      $this->em->persist($customer);
    } else {
      //TODO: setup customer notes and note any differences
      // Emails do not match
      if ($name != $customerExists->getName()) {
        //TODO: do something
        //$customerExists->setName($customer->getName());
      }
      if ($email != $customerExists->getEmail()) {
        //TODO: do something
      }
      // Phones do not match
      if ($phone != $customerExists->getPhone()) {
        //TODO: do something
        $customerExists->setPhone($phone);
      }
      $customer = $customerExists;
    }
    return $customer;
  }

  /**
   * 
   * @param string $email
   * @return Customers|null The customer entity instance or NULL if the entity can not be found.
   */
  public function checkCustomerExistsByEmail($email)
  {
    return $this->repo->findOneBy(array('email' => $email));
  }

}
