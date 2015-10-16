<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderCompanions
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\OrderCompanionsRepository")
 */
class OrderCompanions
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
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="OrderCommpanions")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     *
     */
    private $orderRef;

    /**
     * @ORM\ManyToOne(targetEntity="MeVisa\CRMBundle\Entity\Customer", inversedBy="OrderCompanions")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

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
     * @return orderCompanions
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
     * Set customer
     *
     * @param integer $customer
     * @return orderCompanions
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

}
