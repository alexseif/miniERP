<?php

namespace MeVisa\CRMBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerPassport
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\CRMBundle\Entity\CustomerPassportRepository")
 */
class CustomerPassport
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
     * @ORM\Column(name="passport_number", type="string", length=255)
     */
    private $passportNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="passport_expiry", type="string", length=255)
     */
    private $passportExpiry;

    /**
     * @ORM\OneToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * */
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
     * Set passportNumber
     *
     * @param string $passportNumber
     * @return CustomerPassport
     */
    public function setPassportNumber($passportNumber)
    {
        $this->passportNumber = $passportNumber;

        return $this;
    }

    /**
     * Get passportNumber
     *
     * @return string 
     */
    public function getPassportNumber()
    {
        return $this->passportNumber;
    }

    /**
     * Set passportExpiry
     *
     * @param string $passportExpiry
     * @return CustomerPassport
     */
    public function setPassportExpiry($passportExpiry)
    {
        $this->passportExpiry = $passportExpiry;

        return $this;
    }

    /**
     * Get passportExpiry
     *
     * @return string 
     */
    public function getPassportExpiry()
    {
        return $this->passportExpiry;
    }

    /**
     * Set customer
     *
     * @param \MeVisa\CRMBundle\Entity\Customer $customer
     * @return CustomerPassport
     */
    public function setCustomer(\MeVisa\CRMBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \MeVisa\CRMBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
