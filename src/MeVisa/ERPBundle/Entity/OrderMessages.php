<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderMessages
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\OrderMessagesRepository")
 */
class OrderMessages
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
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="messages")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     */
    private $orderRef;

    /**
     * @ORM\ManyToOne(targetEntity="OrderProducts", inversedBy="messages")
     * @ORM\JoinColumn(name="order_product_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     */
    private $orderProduct;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="read_message", type="boolean")
     */
    private $read;

    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
        $this->read = FALSE;
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
     * @return OrderMessages
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
     * Set type
     *
     * @param string $type
     * @return OrderMessages
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return OrderMessages
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set orderProduct
     *
     * @param \MeVisa\ERPBundle\Entity\OrderProducts $orderProduct
     * @return OrderMessages
     */
    public function setOrderProduct(\MeVisa\ERPBundle\Entity\OrderProducts $orderProduct = null)
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }

    /**
     * Get orderProduct
     *
     * @return \MeVisa\ERPBundle\Entity\OrderProducts 
     */
    public function getOrderProduct()
    {
        return $this->orderProduct;
    }

    /**
     * Set read
     *
     * @param boolean $read
     * @return OrderMessages
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function getRead()
    {
        return $this->read;
    }

}
