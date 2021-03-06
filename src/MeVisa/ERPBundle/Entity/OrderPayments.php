<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrderPayments
 *
 * @ORM\Table()
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\OrderPaymentsRepository")
 */
class OrderPayments
{

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="Orders", inversedBy="orderPayments")
   * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
   * */
  private $orderRef;

  /**
   * @var string
   *
   * @Gedmo\Versioned
   * @ORM\Column(name="method", type="string", length=255, nullable=true)
   */
  private $method;

  /**
   * @var integer
   *
   * @Gedmo\Versioned
   * @ORM\Column(name="amount", type="integer", nullable=true)
   */
  private $amount;

  /**
   * @var string
   *
   * @Gedmo\Versioned
   * @ORM\Column(name="state", type="string", length=255)
   */
  private $state;

  /**
   * @var string
   *
   * @Gedmo\Versioned
   * @ORM\Column(name="detail", type="text", nullable=true)
   */
  private $detail;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime", nullable=true)
   */
  private $createdAt;

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set orderRef
   *
   * @param integer $orderRef
   * @return orderPayments
   */
  public function setOrderRef($orderRef)
  {
    $this->orderRef = $orderRef;

    return $this;
  }

  /**
   * Get orderRef
   *
   * @return integer 
   */
  public function getOrderRef()
  {
    return $this->orderRef;
  }

  /**
   * Set method
   *
   * @param string $method
   * @return orderPayments
   */
  public function setMethod($method)
  {
    $this->method = $method;

    return $this;
  }

  /**
   * Get method
   *
   * @return string 
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * Set amount
   *
   * @param integer $amount
   * @return orderPayments
   */
  public function setAmount($amount)
  {
    $this->amount = $amount;

    return $this;
  }

  /**
   * Get amount
   *
   * @return integer 
   */
  public function getAmount()
  {
    return $this->amount;
  }

  /**
   * Set state
   *
   * @param string $state
   * @return orderPayments
   */
  public function setState($state)
  {
    $this->state = $state;

    return $this;
  }

  /**
   * Get state
   *
   * @return string 
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * Set detail
   *
   * @param string $detail
   * @return orderPayments
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;

    return $this;
  }

  /**
   * Get detail
   *
   * @return string 
   */
  public function getDetail()
  {
    return $this->detail;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return orderPayments
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt
   *
   * @return \DateTime 
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  public function __toString()
  {
    return $this->orderRef;
  }

}
