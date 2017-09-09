<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace MeVisa\ERPBundle\Command;

/**
 * Description of MiniERPDefinitions
 *
 * @author Alex Seif <me@alexseif.com>
 */
class OrdersAPI
{

  public function setCustomer($name, $email, $phone, $agent = null)
  {
    $customer = new \MeVisa\CRMBundle\Entity\Customers();
    $customer->setName($name);
    $customer->setEmail($email);
    $customer->setPhone($phone);
    $customer->setAgent($agent);
    return $customer;
  }

}
