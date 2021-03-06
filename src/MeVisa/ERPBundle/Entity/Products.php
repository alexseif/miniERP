<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Products
 *
 * @ORM\Table()
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\ProductsRepository")
 * 
 */
class Products
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
   * @ORM\Column(name="wc_id", type="string", nullable=true)
   */
  private $wcId;

  /**
   * @var string
   *
   * @Gedmo\Versioned
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="country", type="string", length=255, nullable=true)
   */
  private $country;

  /**
   * @var array
   *
   * @ORM\Column(name="requiredDocuments", type="array")
   */
  private $requiredDocuments;

  /**
   * @var boolean
   *
   * @Gedmo\Versioned
   * @ORM\Column(name="enabled", type="boolean")
   */
  private $enabled;

  /**
   * @var boolean
   * this indicates if the quantity should be taken into consideration, 
   * or should be ignored and calculated based on the stored cost
   * @ORM\Column(name="wc_calc", type="boolean")
   */
  private $wcCalc = false;

  /**
   * @var boolean
   *
   * @ORM\Column(name="urgent", type="boolean")
   */
  private $urgent;

  /**
   * Many Products have Many Vendors.
   * @ORM\ManyToMany(targetEntity="Vendors", inversedBy="products")
   * @ORM\JoinTable(name="product_vendors",
   *      joinColumns={@ORM\JoinColumn(name="products_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="vendors_id", referencedColumnName="id")}
   *      )
   */
  protected $vendors;

  /**
   * @ORM\OneToMany(targetEntity="ProductPrices", mappedBy="product", cascade={"persist"})
   * 
   * */
  private $pricing;

  /**
   * @ORM\OneToMany(targetEntity="OrderProducts", mappedBy="product")
   */
  private $orderProducts;

  public function __construct()
  {
    $this->urgent = false;
    $this->pricing = new ArrayCollection();
    $this->vendors = new ArrayCollection();
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
   * @return Products
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
   * Set requiredDocuments
   *
   * @param array $requiredDocuments
   * @return Products
   */
  public function setRequiredDocuments($requiredDocuments)
  {
    $this->requiredDocuments = $requiredDocuments;

    return $this;
  }

  /**
   * Get requiredDocuments
   *
   * @return array 
   */
  public function getRequiredDocuments()
  {
    return $this->requiredDocuments;
  }

  /**
   * Set enabled
   *
   * @param boolean $enabled
   * @return Products
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;

    return $this;
  }

  /**
   * Get enabled
   *
   * @return boolean 
   */
  public function getEnabled()
  {
    return $this->enabled;
  }

  function getWcId()
  {
    return $this->wcId;
  }

  function setWcId($wcId)
  {
    $this->wcId = $wcId;
  }

  /**
   * Add pricing
   *
   * @param \MeVisa\ERPBundle\Entity\ProductPrices $pricing
   * @return Products
   */
  public function addPricing(\MeVisa\ERPBundle\Entity\ProductPrices $pricing)
  {
    $this->pricing[] = $pricing;
    $pricing->setProduct($this);
    return $this;
  }

  /**
   * Remove pricing
   *
   * @param \MeVisa\ERPBundle\Entity\ProductPrices $pricing
   */
  public function removePricing(\MeVisa\ERPBundle\Entity\ProductPrices $pricing)
  {
    $this->pricing->removeElement($pricing);
  }

  /**
   * Get pricing
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getPricing()
  {
    return $this->pricing;
  }

  /**
   * Set urgent
   *
   * @param boolean $urgent
   * @return Products
   */
  public function setUrgent($urgent)
  {
    $this->urgent = $urgent;

    return $this;
  }

  /**
   * Get urgent
   *
   * @return boolean 
   */
  public function getUrgent()
  {
    return $this->urgent;
  }

  /**
   * Add orderProducts
   *
   * @param \MeVisa\ERPBundle\Entity\OrderProducts $orderProducts
   * @return Products
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

  /**
   * Set wcCalc
   *
   * @param boolean $wcCalc
   * @return Products
   */
  public function setWcCalc($wcCalc)
  {
    $this->wcCalc = $wcCalc;

    return $this;
  }

  /**
   * Get wcCalc
   *
   * @return boolean 
   */
  public function getWcCalc()
  {
    return $this->wcCalc;
  }

  /**
   * Set country
   *
   * @param string $country
   * @return Products
   */
  public function setCountry($country)
  {
    $this->country = $country;

    return $this;
  }

  /**
   * Get country
   *
   * @return string 
   */
  public function getCountry()
  {
    return $this->country;
  }

  /**
   * Get country And Name
   *
   * @return string 
   */
  public function getCountryAndName()
  {
    return (($this->country) ? $this->country . " | " : "") . $this->getName();
  }

  /**
   * Add vendors
   *
   * @param \MeVisa\ERPBundle\Entity\Vendors $vendors
   * @return Products
   */
  public function addVendor(\MeVisa\ERPBundle\Entity\Vendors $vendors)
  {
    $this->vendors[] = $vendors;

    return $this;
  }

  /**
   * Remove vendors
   *
   * @param \MeVisa\ERPBundle\Entity\Vendors $vendors
   */
  public function removeVendor(\MeVisa\ERPBundle\Entity\Vendors $vendors)
  {
    $this->vendors->removeElement($vendors);
  }

  /**
   * Get vendors
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getVendors()
  {
    return $this->vendors;
  }

  /**
   * 
   * @param \MeVisa\ERPBundle\Entity\Vendors $vendor
   * @return bool
   */
  public function hasVendor(Vendors $vendor)
  {
    return $this->getVendors()->contains($vendor);
  }

  public function __toString()
  {
    return (string) $this->getName();
  }

}
