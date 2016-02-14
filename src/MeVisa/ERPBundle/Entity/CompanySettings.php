<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanySettings
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\CompanySettingsRepository")
 */
class CompanySettings
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
     * @var array
     *
     * @ORM\Column(name="jsonValue", type="json_array")
     */
    private $jsonValue;

    public function __construct()
    {
        $this->jsonValue = array();
        $this->jsonValue['name'] = '';
        $this->jsonValue['bank'] = '';
        $this->jsonValue['agreement'] = '';
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
     * @return CompanySettings
     */
    public function setName($name)
    {
        $this->jsonValue['name'] = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->jsonValue['name'];
    }

    /**
     * Set bank
     *
     * @param string $bank
     * @return CompanySettings
     */
    public function setBank($bank)
    {
        $this->jsonValue['bank'] = $bank;

        return $this;
    }

    /**
     * Get bank
     *
     * @return string 
     */
    public function getBank()
    {
        return $this->jsonValue['bank'];
    }

    /**
     * Set agreement
     *
     * @param string $agreement
     * @return CompanySettings
     */
    public function setAgreement($agreement)
    {
        $this->jsonValue['agreement'] = $agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return string 
     */
    public function getAgreement()
    {
        return $this->jsonValue['agreement'];
    }

    /**
     * Set jsonValue
     *
     * @param array $jsonValue
     * @return CompanySettings
     */
    public function setJsonValue($jsonValue)
    {
        $this->jsonValue = $jsonValue;

        return $this;
    }

    /**
     * Get jsonValue
     *
     * @return array 
     */
    public function getJsonValue()
    {
        return $this->jsonValue;
    }

}
