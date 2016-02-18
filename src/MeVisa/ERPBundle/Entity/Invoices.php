<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Invoices
 *
 * @ORM\Table()
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\InvoicesRepository")
 */
class Invoices
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
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="invoices")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $orderRef;

    /**
     * @var text
     * @Gedmo\Versioned
     * @ORM\Column(name="customer_signature", type="text", nullable=true)
     */
    private $customerSignature;

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
     * @param \MeVisa\ERPBundle\Entity\Orders $orderRef
     * @return Invoices
     */
    public function setOrderRef(\MeVisa\ERPBundle\Entity\Orders $orderRef = null)
    {
        $this->orderRef = $orderRef;

        return $this;
    }

    /**
     * Get orderRef
     *
     * @return \MeVisa\ERPBundle\Entity\Orders 
     */
    public function getOrderRef()
    {
        return $this->orderRef;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Invoices
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
     * Set customerSignature
     *
     * @param string $customerSignature
     * @return Invoices
     */
    public function setCustomerSignature($customerSignature)
    {
        $this->customerSignature = $customerSignature;

        return $this;
    }

    /**
     * Get customerSignature
     *
     * @return string 
     */
    public function getCustomerSignature()
    {
        return $this->customerSignature;
    }

}
