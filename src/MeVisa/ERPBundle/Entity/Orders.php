<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Orders
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\OrdersRepository")
 */
class Orders
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
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     *
     * @ORM\ManyToOne(targetEntity="MeVisa\CRMBundle\Entity\Customer", inversedBy="Orders", cascade={"persist"})
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=255)
     */
    private $channel;

    /**
     * @var integer
     *
     * @ORM\Column(name="productsTotal", type="integer")
     */
    private $productsTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="adjustmentTotal", type="integer")
     */
    private $adjustmentTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedAt", type="datetime")
     */
    private $deletedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="completedAt", type="datetime")
     */
    private $completedAt;

    /**
     * @ORM\OneToMany(targetEntity="OrderProducts", mappedBy="orderRef")
     * */
    private $orderProducts;

    /**
     * @ORM\OneToMany(targetEntity="OrderPayments", mappedBy="orderRef")
     * */
    private $orderPayments;

    /**
     * @ORM\OneToMany(targetEntity="OrderCompanions", mappedBy="orderRef")
     * */
    private $orderCompanions;

    /**
     * @ORM\OneToMany(targetEntity="OrderComments", mappedBy="orderRef")
     * */
    private $orderComments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->companions = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * Set number
     *
     * @param string $number
     * @return Orders
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Orders
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

    public function getCustomers()
    {
        
    }

    /**
     * Set customer
     *
     * @param integer $customer
     * @return Orders
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return integer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return Orders
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string 
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set products
     *
     * @param guid $products
     * @return Orders
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Get products
     *
     * @return guid 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set productsTotal
     *
     * @param integer $productsTotal
     * @return Orders
     */
    public function setProductsTotal($productsTotal)
    {
        $this->productsTotal = $productsTotal;

        return $this;
    }

    /**
     * Get productsTotal
     *
     * @return integer 
     */
    public function getProductsTotal()
    {
        return $this->productsTotal;
    }

    /**
     * Set adjustmentTotal
     *
     * @param integer $adjustmentTotal
     * @return Orders
     */
    public function setAdjustmentTotal($adjustmentTotal)
    {
        $this->adjustmentTotal = $adjustmentTotal;

        return $this;
    }

    /**
     * Get adjustmentTotal
     *
     * @return integer 
     */
    public function getAdjustmentTotal()
    {
        return $this->adjustmentTotal;
    }

    /**
     * Set total
     *
     * @param integer $total
     * @return Orders
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Orders
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Orders
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Orders
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set completedAt
     *
     * @param \DateTime $completedAt
     * @return Orders
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt
     *
     * @return \DateTime 
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Add orderProducts
     *
     * @param \MeVisa\ERPBundle\Entity\OrderProducts $orderProducts
     * @return Orders
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
     * Add orderPayments
     *
     * @param \MeVisa\ERPBundle\Entity\OrderPayments $orderPayments
     * @return Orders
     */
    public function addOrderPayment(\MeVisa\ERPBundle\Entity\OrderPayments $orderPayments)
    {
        $this->orderPayments[] = $orderPayments;

        return $this;
    }

    /**
     * Remove orderPayments
     *
     * @param \MeVisa\ERPBundle\Entity\OrderPayments $orderPayments
     */
    public function removeOrderPayment(\MeVisa\ERPBundle\Entity\OrderPayments $orderPayments)
    {
        $this->orderPayments->removeElement($orderPayments);
    }

    /**
     * Get orderPayments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderPayments()
    {
        return $this->orderPayments;
    }

    /**
     * Add orderCompanions
     *
     * @param \MeVisa\ERPBundle\Entity\OrderCompanions $orderCompanions
     * @return Orders
     */
    public function addOrderCompanion(\MeVisa\ERPBundle\Entity\OrderCompanions $orderCompanions)
    {
        $this->orderCompanions[] = $orderCompanions;

        return $this;
    }

    /**
     * Remove orderCompanions
     *
     * @param \MeVisa\ERPBundle\Entity\OrderCompanions $orderCompanions
     */
    public function removeOrderCompanion(\MeVisa\ERPBundle\Entity\OrderCompanions $orderCompanions)
    {
        $this->orderCompanions->removeElement($orderCompanions);
    }

    /**
     * Get orderCompanions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderCompanions()
    {
        return $this->orderCompanions;
    }

    /**
     * Add orderComments
     *
     * @param \MeVisa\ERPBundle\Entity\OrderComments $orderComments
     * @return Orders
     */
    public function addOrderComment(\MeVisa\ERPBundle\Entity\OrderComments $orderComments)
    {
        $this->orderComments[] = $orderComments;

        return $this;
    }

    /**
     * Remove orderComments
     *
     * @param \MeVisa\ERPBundle\Entity\OrderComments $orderComments
     */
    public function removeOrderComment(\MeVisa\ERPBundle\Entity\OrderComments $orderComments)
    {
        $this->orderComments->removeElement($orderComments);
    }

    /**
     * Get orderComments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderComments()
    {
        return $this->orderComments;
    }

}
