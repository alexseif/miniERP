<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductsRepository
 *
 */
class ProductsRepository extends EntityRepository
{

    public function queryAllEnabled()
    {
        return $this->createQueryBuilder('p')
                        ->Join('p.pricing', 'pp')
                        ->where('p.enabled = true')
                        ->orderBy('p.id ASC, pp.createdAt', 'DESC')
                        ;
    }

    public function findAllEnabled()
    {
        return $this->queryAllEnabled()
                        ->getQuery()
                        ->getResult();
    }

}
