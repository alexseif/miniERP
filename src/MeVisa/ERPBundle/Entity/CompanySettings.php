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
        $this->jsonValue['address'] = '';
        $this->jsonValue['bank'] = '';
        $this->jsonValue['invoiceSignature1'] = '';
        $this->jsonValue['invoiceSignature2'] = '';
        $this->jsonValue['agreement'] = '';
        $this->jsonValue['agreementSignature'] = '';
        $this->jsonValue['agreementSignatureName'] = '';
        $this->jsonValue['waiver'] = '';
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
     * Set address
     *
     * @param string $address
     * @return CompanySettings
     */
    public function setAddress($address)
    {
        $this->jsonValue['address'] = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->jsonValue['address'];
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

    /**
     * Set invoiceSignature1
     *
     * @param string $InvoiceSignature1
     * @return CompanySettings
     */
    public function setInvoiceSignature1($InvoiceSignature1)
    {
        $this->jsonValue['invoiceSignature1'] = $InvoiceSignature1;

        return $this;
    }

    /**
     * Get invoiceSignature1
     *
     * @return string 
     */
    public function getInvoiceSignature1()
    {
        return $this->jsonValue['invoiceSignature1'];
    }

    /**
     * Set invoiceSignature2
     *
     * @param string $InvoiceSignature2
     * @return CompanySettings
     */
    public function setInvoiceSignature2($InvoiceSignature2)
    {
        $this->jsonValue['invoiceSignature2'] = $InvoiceSignature2;

        return $this;
    }

    /**
     * Get invoiceSignature2
     *
     * @return string 
     */
    public function getInvoiceSignature2()
    {
        return $this->jsonValue['invoiceSignature2'];
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
     * Set agreementSignature
     *
     * @param string $agreementSignature
     * @return CompanySettings
     */
    public function setAgreementSignature($agreementSignature)
    {
        $this->jsonValue['agreementSignature'] = $agreementSignature;

        return $this;
    }

    /**
     * Get agreementSignature
     *
     * @return string 
     */
    public function getAgreementSignature()
    {
        return $this->jsonValue['agreementSignature'];
    }

    /**
     * Set agreementSignatureName
     *
     * @param string $agreementSignatureName
     * @return CompanySettings
     */
    public function setAgreementSignatureName($agreementSignatureName)
    {
        $this->jsonValue['agreementSignatureName'] = $agreementSignatureName;

        return $this;
    }

    /**
     * Get agreementSignatureName
     *
     * @return string 
     */
    public function getAgreementSignatureName()
    {
        return $this->jsonValue['agreementSignatureName'];
    }

    /**
     * Set waiver
     *
     * @param string $waiver
     * @return CompanySettings
     */
    public function setWaiver($waiver)
    {
        $this->jsonValue['waiver'] = $waiver;

        return $this;
    }

    /**
     * Get waiver
     *
     * @return string 
     */
    public function getWaiver()
    {
        return $this->jsonValue['waiver'];
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
