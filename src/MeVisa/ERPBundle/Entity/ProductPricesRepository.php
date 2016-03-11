<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductPricesRepository
 *
 */
class ProductPricesRepository extends EntityRepository
{

    public function findAllPrices()
    {
        return $this->createQueryBuilder('pp')
                        ->orderBy('pp.createdAt', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

}
