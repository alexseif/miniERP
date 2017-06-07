<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace MeVisa\ERPBundle\Command;

/**
 * Description of MODxTranslator
 *
 * @author Alex Seif <me@alexseif.com>
 */
class MODxTranslator
{

  public $modxOrder;

  public function __construct($modxOrder)
  {
    $this->modxOrder = $modxOrder;
  }

  public function getName()
  {
    return $this->modxOrder['name'] . ' ' . $this->modxOrder['famil'];
  }

  public function getEmail()
  {
    return $this->modxOrder['email'];
  }

  public function getPhone()
  {
    return $this->modxOrder['phone'];
  }

  public function getWcId()
  {
    return $this->modxOrder['id'];
  }

  public function getOrderNumber()
  {
    return 'UV' . $this->modxOrder['id'];
  }

  public function getCreatedAt()
  {
    $timezone = new \DateTimeZone('UTC');
    return new \DateTime($this->modxOrder['createdon'], $timezone);
  }

  public function getStatus()
  {
    return "backoffice";
  }

  public function getPeople()
  {
    return $this->modxOrder['kolchel'];
  }

  public function getTotal()
  {
    return $this->modxOrder['cost'] * 100;
  }

  public function getProductsTotal()
  {
    return $this->modxOrder['cost'] * 100;
  }

  public function getAdjustmentTotal()
  {
    return 0;
  }

  public function getProductName($modxOrder)
  {
    return $modxOrder['namezakaz'];
  }

  public function getProductPrice($modxOrder)
  {
    return ($modxOrder['cost'] * 100) / $modxOrder['kolchel'];
  }

  public function getProductQuantity($modxOrder)
  {
    return $modxOrder['kolchel'];
  }

  public function getProductTotal()
  {
    return $modxOrder['cost'] * 100;
  }

}
