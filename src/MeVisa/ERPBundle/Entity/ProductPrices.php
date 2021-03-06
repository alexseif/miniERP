<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProductPrices
 *
 * @ORM\Table()
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\ProductPricesRepository")
 */
class ProductPrices
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
   * @var integer
   *
   * @ORM\Column(name="cost", type="integer")
   */
  private $cost;

  /**
   * @var integer
   *
   * @ORM\Column(name="price", type="integer")
   */
  private $price;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\ManyToOne(targetEntity="Products", inversedBy="pricing")
   * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
   * */
  private $product;

  function __construct()
  {
    $this->setCreatedAt(new \DateTime());
  }

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
   * Set cost
   *
   * @param integer $cost
   * @return ProductPrices
   */
  public function setCost($cost)
  {
    $this->cost = $cost;

    return $this;
  }

  /**
   * Get cost
   *
   * @return integer 
   */
  public function getCost()
  {
    return $this->cost;
  }

  /**
   * Set price
   *
   * @param integer $price
   * @return ProductPrices
   */
  public function setPrice($price)
  {
    $this->price = $price;

    return $this;
  }

  /**
   * Get price
   *
   * @return integer 
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return ProductPrices
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

  /**
   * Set product
   *
   * @param string $product
   * @return ProductPrices
   */
  public function setProduct($product)
  {
    $this->product = $product;

    return $this;
  }

  /**
   * Get product
   *
   * @return string 
   */
  public function getProduct()
  {
    return $this->product;
  }

}
