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
     * @var integer
     *
     * @ORM\Column(name="customer", type="integer")
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
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="OrderPayments", mappedBy="orderRef")
     * */
    private $payments;

    /**
     * @ORM\OneToMany(targetEntity="OrderCompanions", mappedBy="orderRef")
     * */
    private $companions;

    /**
     * @ORM\OneToMany(targetEntity="OrderComments", mappedBy="orderRef")
     * */
    private $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->companions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add products
     *
     * @param \MeVisa\ERPBundle\Entity\orderProducts $products
     * @return Orders
     */
    public function addProduct(\MeVisa\ERPBundle\Entity\orderProducts $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \MeVisa\ERPBundle\Entity\orderProducts $products
     */
    public function removeProduct(\MeVisa\ERPBundle\Entity\orderProducts $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Add payments
     *
     * @param \MeVisa\ERPBundle\Entity\orderPayments $payments
     * @return Orders
     */
    public function addPayment(\MeVisa\ERPBundle\Entity\orderPayments $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \MeVisa\ERPBundle\Entity\orderPayments $payments
     */
    public function removePayment(\MeVisa\ERPBundle\Entity\orderPayments $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Add companions
     *
     * @param \MeVisa\ERPBundle\Entity\orderCompanions $companions
     * @return Orders
     */
    public function addCompanion(\MeVisa\ERPBundle\Entity\orderCompanions $companions)
    {
        $this->companions[] = $companions;

        return $this;
    }

    /**
     * Remove companions
     *
     * @param \MeVisa\ERPBundle\Entity\orderCompanions $companions
     */
    public function removeCompanion(\MeVisa\ERPBundle\Entity\orderCompanions $companions)
    {
        $this->companions->removeElement($companions);
    }

    /**
     * Get companions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompanions()
    {
        return $this->companions;
    }

    /**
     * Add comments
     *
     * @param \MeVisa\ERPBundle\Entity\orderComments $comments
     * @return Orders
     */
    public function addComment(\MeVisa\ERPBundle\Entity\orderComments $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \MeVisa\ERPBundle\Entity\orderComments $comments
     */
    public function removeComment(\MeVisa\ERPBundle\Entity\orderComments $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

}
