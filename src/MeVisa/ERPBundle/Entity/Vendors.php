<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Vendors
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Vendors
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
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="code", type="string", length=255)
   */
  private $code;

  /**
   * Many Vendors have Many products.
   * @ORM\ManyToMany(targetEntity="Products", mappedBy="vendors")
   */
  protected $products;

  /**
   * @ORM\OneToMany(targetEntity="OrderProducts", mappedBy="vendor")
   */
  protected $orderProducts;

  public function __construct()
  {
    $this->products = new ArrayCollection();
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
   * Set name
   *
   * @param string $name
   * @return Vendors
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set code
   *
   * @param string $code
   * @return Vendors
   */
  public function setCode($code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string 
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set products
   *
   * @param string $products
   * @return Vendors
   */
  public function setProducts($products)
  {
    $this->products = $products;

    return $this;
  }

  /**
   * Get products
   *
   * @return string 
   */
  public function getProducts()
  {
    return $this->products;
  }

  /**
   * Add products
   *
   * @param \MeVisa\ERPBundle\Entity\Products $products
   * @return Vendors
   */
  public function addProduct(\MeVisa\ERPBundle\Entity\Products $products)
  {
    $this->products[] = $products;
    $products->addVendor($this);

    return $this;
  }

  /**
   * Remove products
   *
   * @param \MeVisa\ERPBundle\Entity\Products $products
   */
  public function removeProduct(\MeVisa\ERPBundle\Entity\Products $products)
  {
    $this->products->removeElement($products);
  }

  /**
   * 
   * @param \MeVisa\ERPBundle\Entity\Products $product
   * @return bool
   */
  public function hasProduct(Products $product)
  {
    return $this->getProducts()->contains($product);
  }

  /**
   * Add orderProducts
   *
   * @param \MeVisa\ERPBundle\Entity\OrderProducts $orderProducts
   * @return Vendors
   */
  public function addOrderProduct(\MeVisa\ERPBundle\Entity\OrderProducts $orderProducts)
  {
    $this->orderProducts[] = $orderProducts;

    return $this;
  }

  /**
   * Remove orderProducts
   *
   * @param \MeVisa\ERPBundle\Entity\OrderProducts $orderProducts
   */
  public function removeOrderProduct(\MeVisa\ERPBundle\Entity\OrderProducts $orderProducts)
  {
    $this->orderProducts->removeElement($orderProducts);
  }

  /**
   * Get orderProducts
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getOrderProducts()
  {
    return $this->orderProducts;
  }

  public function __toString()
  {
    return (string) $this->getName();
  }

}
