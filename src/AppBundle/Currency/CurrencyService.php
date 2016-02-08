<?php

namespace AppBundle\Currency;
use Doctrine\ORM\EntityManager;
//use AppBundle\Entity\Currency;

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
