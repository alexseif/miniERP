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

  /**
   * 
   * @param int $productId
   * @param datetime $date
   * @return type
   */
  public function findProductPriceAtDate($productId, $date)
  {
    return $this->createQueryBuilder('pp')
            ->where('pp.product = ?1 AND pp.createdAt <= ?2 ')
            ->orderBy('pp.createdAt', 'DESC')
            ->setParameter('1', $productId, \Doctrine\DBAL\Types\Type::INTEGER)
            ->setParameter('2', $date, \Doctrine\DBAL\Types\Type::DATETIME)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
  }

}
