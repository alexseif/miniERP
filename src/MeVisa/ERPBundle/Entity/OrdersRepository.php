<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * OrdersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrdersRepository extends EntityRepository
{

    public function findAllByState($state)
    {
        return $this->createQueryBuilder('o')
                        ->where("o.state = ?1")
                        ->setParameter('1', $state)
                        ->getQuery()
                        ->getResult();
    }
    public function findAllPending()
    {
        return $this->createQueryBuilder('o')
                        ->where("o.state = ?1")
                        ->orWhere("o.state = ?2")
                        ->setParameter('1', 'pending')
                        ->setParameter('2', 'processing')
                        ->getQuery()
                        ->getResult();
    }

    public function queryLastPOSNumber()
    {
        return $this->createQueryBuilder("o")
                        ->where("o.number LIKE 'POS%'")
                        ->orderBy("o.id", "DESC")
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
    }

    public function searchQuery($text)
    {

        return $this->createQueryBuilder("o")
                        ->leftJoin("o.customer", 'c')
//                        ->("c.id = o.customer")
                        ->where("c.name LIKE ?1")
                        ->setParameter("1", "%".$text)
                        ->orWhere("c.name LIKE ?2")
                        ->setParameter("2", $text."%")
                        ->getQuery()
                        ->getResult();
    }

}
