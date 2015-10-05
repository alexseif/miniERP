<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Products
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Products
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="requiredDocuments", type="array")
     */
    private $requiredDocuments;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity="Vendors", inversedBy="products")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     */
    protected $vendor;

    /**
     * @ORM\OneToMany(targetEntity="ProductPrices", mappedBy="product")
     * */
    private $pricing;

    public function __construct()
    {
        $this->pricing = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Products
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
     * Set requiredDocuments
     *
     * @param array $requiredDocuments
     * @return Products
     */
    public function setRequiredDocuments($requiredDocuments)
    {
        $this->requiredDocuments = $requiredDocuments;

        return $this;
    }

    /**
     * Get requiredDocuments
     *
     * @return array 
     */
    public function getRequiredDocuments()
    {
        return $this->requiredDocuments;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Products
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set vendor
     *
     * @param string $vendor
     * @return Products
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return string 
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Add pricing
     *
     * @param \MeVisa\ERPBundle\Entity\ProductPrices $pricing
     * @return Products
     */
    public function addPricing(\MeVisa\ERPBundle\Entity\ProductPrices $pricing)
    {
        $this->pricing[] = $pricing;

        return $this;
    }

    /**
     * Remove pricing
     *
     * @param \MeVisa\ERPBundle\Entity\ProductPrices $pricing
     */
    public function removePricing(\MeVisa\ERPBundle\Entity\ProductPrices $pricing)
    {
        $this->pricing->removeElement($pricing);
    }

    /**
     * Get pricing
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPricing()
    {
        return $this->pricing;
    }

}
