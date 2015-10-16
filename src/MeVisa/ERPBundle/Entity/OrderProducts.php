<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderProducts
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\OrderProductsRepository")
 */
class OrderProducts
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
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="OrderProducts")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * */
    private $orderRef;

    /**
     * @ORM\ManyToOne(targetEntity="Products", inversedBy="OrderPayments")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * */
    private $product;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="unitPrice", type="integer")
     */
    private $unitPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

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
     * @return orderProducts
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
     * Set product
     *
     * @param integer $product
     * @return orderProducts
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return integer 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set age
     *
     * @param integer $age
     * @return orderProducts
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return orderProducts
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set unitPrice
     *
     * @param integer $unitPrice
     * @return orderProducts
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return integer 
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set total
     *
     * @param integer $total
     * @return orderProducts
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer 
     */
    public function getTotal()
    {
        return $this->total;
    }

}