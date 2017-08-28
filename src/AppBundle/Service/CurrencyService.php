<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class CurrencyService
{

  protected $em;

  /**
   * @InjectParams({
   *    "em" = @Inject("doctrine.orm.entity_manager")
   * })
   */
  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public function getCurrency()
  {
    return $this
            ->em
            ->getRepository('AppBundle:Currency')
            ->getToDate();
//        return '7400';
  }

}
