<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPrices
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @var string
     *
     * @ORM\Column(name="age", type="string", length=255)
     */
    private $age;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float")
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="costCurrency", type="string", length=4)
     */
    private $costCurrency;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="priceCurrency", type="string", length=4)
     */
    private $priceCurrency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Products", inversedBy="ProductPrices")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * */
    private $product;

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
     * Set age
     *
     * @param string $age
     * @return ProductPrices
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return string 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set cost
     *
     * @param float $cost
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
     * @return float 
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set costCurrency
     *
     * @param string $costCurrency
     * @return ProductPrices
     */
    public function setCostCurrency($costCurrency)
    {
        $this->costCurrency = $costCurrency;

        return $this;
    }

    /**
     * Get costCurrency
     *
     * @return string 
     */
    public function getCostCurrency()
    {
        return $this->costCurrency;
    }

    /**
     * Set price
     *
     * @param float $price
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
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set priceCurrency
     *
     * @param string $priceCurrency
     * @return ProductPrices
     */
    public function setPriceCurrency($priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;

        return $this;
    }

    /**
     * Get priceCurrency
     *
     * @return string 
     */
    public function getPriceCurrency()
    {
        return $this->priceCurrency;
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
