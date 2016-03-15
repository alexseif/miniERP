<?php

namespace MeVisa\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Customers
 *
 * @ORM\Table()
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="MeVisa\CRMBundle\Entity\CustomersRepository")
 */
class Customers
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
     * @Gedmo\Versioned
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/u")
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="phone", type="string", length=25)
     */
    private $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="agent", type="boolean", nullable=true)
     */
    private $agent;
    /**
     *
     * @ORM\OneToMany(targetEntity="MeVisa\ERPBundle\Entity\Orders", mappedBy="customer", cascade={"persist"})
     */
    private $orders;

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
     * @return Customers
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
     * Set email
     *
     * @param string $email
     * @return Customers
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Customers
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add orders
     *
     * @param \MeVisa\ERPBundle\Entity\Orders $orders
     * @return Customers
     */
    public function addOrder(\MeVisa\ERPBundle\Entity\Orders $orders)
    {
        $this->orders[] = $orders;

        return $this;
    }

    /**
     * Remove orders
     *
     * @param \MeVisa\ERPBundle\Entity\Orders $orders
     */
    public function removeOrder(\MeVisa\ERPBundle\Entity\Orders $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrders()
    {
        return $this->orders;
    }


    /**
     * Set agent
     *
     * @param boolean $agent
     * @return Customers
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return boolean 
     */
    public function getAgent()
    {
        return $this->agent;
    }
}
