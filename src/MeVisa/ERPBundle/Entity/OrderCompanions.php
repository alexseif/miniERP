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
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="passport_number", type="string", length=255, nullable=true)
     */
    private $passportNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="passport_expiry", type="string", length=255, nullable=true)
     */
    private $passportExpiry;

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
     * Set name
     *
     * @param string $name
     * @return OrderCompanions
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
     * Set passportNumber
     *
     * @param string $passportNumber
     * @return OrderCompanions
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
     * @return OrderCompanions
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
     * Set age
     *
     * @param integer $age
     * @return OrderCompanions
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

}
