<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrderProducts
 *
 * @ORM\Table()
 * @Gedmo\Loggable
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
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="orderProducts")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     * */
    private $orderRef;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Products", inversedBy="orderProducts")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="unitPrice", type="integer")
     */
    private $unitPrice;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="unitCost", type="integer")
     */
    private $unitCost;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity="Vendors", inversedBy="orderProducts")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     */
    protected $vendor;

    /**
     * @ORM\OneToMany(targetEntity="OrderMessages", mappedBy="orderProduct", cascade={"persist"})
     * */
    private $messages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set unitCost
     *
     * @param integer $unitCost
     * @return OrderProducts
     */
    public function setUnitCost($unitCost)
    {
        $this->unitCost = $unitCost;

        return $this;
    }

    /**
     * Get unitCost
     *
     * @return integer 
     */
    public function getUnitCost()
    {
        return $this->unitCost;
    }

    /**
     * Set vendor
     *
     * @param \MeVisa\ERPBundle\Entity\Vendors $vendor
     * @return OrderProducts
     */
    public function setVendor(\MeVisa\ERPBundle\Entity\Vendors $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \MeVisa\ERPBundle\Entity\Vendors 
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Add orderMessages
     *
     * @param \MeVisa\ERPBundle\Entity\OrderMessages $orderMessages
     * @return OrderProducts
     */
    public function addMessage(\MeVisa\ERPBundle\Entity\OrderMessages $orderMessages)
    {
        $this->messages[] = $orderMessages;
        $orderMessages->setOrderProduct($this);

        return $this;
    }

    /**
     * Remove orderMessages
     *
     * @param \MeVisa\ERPBundle\Entity\OrderMessages $orderMessages
     */
    public function removeMessage(\MeVisa\ERPBundle\Entity\OrderMessages $orderMessages)
    {
        $this->messages->removeElement($orderMessages);
    }

    /**
     * Get orderMessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function newMessage($type, $message)
    {
        $msg = new OrderMessages($type, $message);
        $this->addMessage($msg);
        $this->getOrderRef()->addMessage($msg);
    }

    public function hasMessages()
    {
        return !$this->getMessages()->isEmpty();
    }

}
