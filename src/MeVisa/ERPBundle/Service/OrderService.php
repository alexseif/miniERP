<?php

namespace MeVisa\ERPBundle\Service;

use Doctrine\ORM\EntityManager;

class OrderService
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

}
