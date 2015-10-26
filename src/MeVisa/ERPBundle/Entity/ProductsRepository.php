<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductsRepository
 *
 */
class ProductsRepository extends EntityRepository
{

    public function queryAllEnabledProducts()
    {
        return $this->createQueryBuilder('p')
                        ->where('p.enabled = true');
    }

    public function getAllEnabledProducts()
    {
        return $this->queryAllEnabledProducts()->getResults();
    }

}
