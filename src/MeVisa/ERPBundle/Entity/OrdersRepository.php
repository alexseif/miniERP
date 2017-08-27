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

  public function find($id)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, op, oc, opa, od, oco, i')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderProducts', 'op')
            ->leftJoin('o.orderCompanions', 'oc')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderDocuments', 'od')
            ->leftJoin('o.orderComments', 'oco')
            ->leftJoin('o.invoices', 'i')
            ->where("o.id = ?1")
            ->setParameter('1', $id)
            ->getQuery()
            ->getOneOrNullResult();
  }

  public function findAll()
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function findCurrentOrdersList()
  {
    $today = new \DateTime("7 days ago");

    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->where('o.completedAt >= ?1')
            ->setParameter('1', $today->format('Y-m-d'))
            ->orWhere('o.completedAt is null')
            ->andWhere("o.deletedAt is null")
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function findArchivedOrdersList()
  {
    $today = new \DateTime("7 days ago");

    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->where('o.completedAt < ?1')
            ->setParameter('1', $today->format('Y-m-d'))
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function findDeletedOrdersList()
  {

    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->where('o.deletedAt IS NOT NULL')
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function findAllByState($state)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->where("o.state = ?1")
            ->andWhere("opa.state = 'paid'")
            ->setParameter('1', $state)
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function findAllComplete()
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->Where("DATE_DIFF(o.completedAt, CURRENT_DATE()) = 0")
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function findAllNotPaid()
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->Where("opa.state != 'paid'")
            ->andWhere("o.completedAt is NULL")
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function findAllPending()
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->where("o.state = ?1")
            ->orWhere("o.state = ?2")
            ->setParameter('1', 'pending')
            ->setParameter('2', 'processing')
            ->orderBy("o.createdAt, o.wcId")
            ->getQuery()
            ->getResult();
  }

  public function queryLastPOSOrder()
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
            ->select('o, c')
            ->leftJoin("o.customer", 'c')
            ->leftJoin("o.orderCompanions", 'oc')
            ->where("c.name LIKE ?1")
            ->orWhere("c.name LIKE ?1")
            ->orWhere("o.number LIKE ?1")
            ->orWhere("c.phone LIKE ?1")
            ->orWhere("c.email LIKE ?1")
            ->orWhere("oc.name LIKE ?1")
            ->orWhere("oc.passportNumber LIKE ?1")
            ->setParameter("1", "%" . $text . "%")
            ->getQuery()
            ->getResult();
  }

  public function findWC()
  {
    return $this->createQueryBuilder("o")
            ->where("o.wcId is not null")
            ->orderBy('o.number', 'ASC')
            ->getQuery()
            ->getResult();
  }

  public function findAllGroupByMonthAndYear()
  {
    return $this->createQueryBuilder('o')
            ->select(' YEAR(o.createdAt) as gBYear, MONTHNAME(o.createdAt) as gBMonth, MONTH(o.createdAt) as gMonth ')
            ->orderBy("o.createdAt, o.wcId")
            ->groupBy('gBYear')
            ->addGroupBy('gBMonth')
            ->getQuery()
            ->getResult();
  }

  public function findByMonthAndYear($month, $year)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->where('o.state != ?4 ')
            ->andWhere('opa.state = ?3 ')
            ->andWhere('MONTHNAME(o.createdAt) = ?1')
            ->andWhere('YEAR(o.createdAt) = ?2')
            ->orderBy('o.createdAt, o.wcId')
            ->setParameter('1', $month)
            ->setParameter('2', $year)
            ->setParameter('3', "PAID")
            ->setParameter('4', "deleted")
            ->getQuery()
            ->getResult();
  }

  public function findByFromAndTo($from, $to)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->where('o.state != ?4 ')
            ->andWhere('opa.state = ?3 ')
            ->andWhere('o.createdAt >= ?1')
            ->andWhere('o.createdAt <= ?2')
            ->orderBy('o.createdAt, o.wcId')
            ->setParameter('1', $from)
            ->setParameter('2', $to)
            ->setParameter('3', "PAID")
            ->setParameter('4', "deleted")
            ->getQuery()
            ->getResult();
  }

  public function findByMonthAndYearNoCash($month, $year)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->where('o.state != ?4 ')
            ->andWhere('opa.state = ?3 ')
            ->andWhere('MONTHNAME(o.createdAt) = ?1')
            ->andWhere('YEAR(o.createdAt) = ?2')
            ->andWhere("opa.method != 'cash'")
            ->orderBy('o.createdAt, o.wcId')
            ->setParameter('1', $month)
            ->setParameter('2', $year)
            ->setParameter('3', "PAID")
            ->setParameter('4', "deleted")
            ->getQuery()
            ->getResult();
  }

  public function findByFromAndToNoCash($from, $to)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->where('o.state = ?4 ')
            ->andWhere('opa.state = ?3 ')
            ->andWhere('o.createdAt >= ?1')
            ->andWhere('o.createdAt <= ?2')
            ->andWhere("opa.method != 'cash'")
            ->orderBy('o.createdAt, o.wcId')
            ->setParameter('1', $from)
            ->setParameter('2', $to)
            ->setParameter('3', "PAID")
            ->setParameter('4', "deleted")
            ->getQuery()
            ->getResult();
  }

  public function findAllGroupByMonthAndYearAndVendor()
  {
    return $this->createQueryBuilder('o')
            ->select('v.id as vendor_id, v.name as vendor_name, YEAR(o.createdAt) as gBYear, MONTHNAME(o.createdAt) as gBMonth, MONTH(o.createdAt) as gMonth ')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.vendor', 'v')
            ->where('o.state != ?4 ')
            ->setParameter('4', "deleted")
            ->orderBy("o.createdAt, o.wcId")
            ->groupBy('opr.vendor')
            ->addGroupBy('gBYear')
            ->addGroupBy('gBMonth')
            ->getQuery()
            ->getResult();
  }

  public function findByMonthAndYearAndVendor($month, $year, $vendor_id)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->where('o.state != ?5 ')
            ->andWhere('opa.state = ?3 ')
            ->andWhere('MONTHNAME(o.createdAt) = ?1')
            ->andWhere('YEAR(o.createdAt) = ?2')
            ->andWhere('opr.vendor = ?4')
            ->orderBy('o.createdAt, o.wcId')
            ->setParameter('1', $month)
            ->setParameter('2', $year)
            ->setParameter('3', "PAID")
            ->setParameter('4', $vendor_id)
            ->setParameter('5', "deleted")
            ->getQuery()
            ->getResult();
  }

  public function findByMonthAndYearAndNoVendor($month, $year)
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->where('opa.state = ?3 ')
            ->andWhere('MONTHNAME(o.createdAt) = ?1')
            ->andWhere('YEAR(o.createdAt) = ?2')
            ->andWhere('opr.vendor IS NULL')
            ->orderBy('o.createdAt, o.wcId')
            ->setParameter('1', $month)
            ->setParameter('2', $year)
            ->setParameter('3', "PAID")
            ->getQuery()
            ->getResult();
  }

  public function findRevenue()
  {
    return $this->createQueryBuilder('o')
            ->select('o, c, opa, opr, p')
            ->leftJoin('o.customer', 'c')
            ->leftJoin('o.orderPayments', 'opa')
            ->leftJoin('o.orderProducts', 'opr')
            ->leftJoin('opr.product', 'p')
            ->Where("o.state = 'approved' OR o.state = 'rejected'")
            ->orderBy("o.createdAt")
            ->getQuery()
            ->getResult();
  }

}
