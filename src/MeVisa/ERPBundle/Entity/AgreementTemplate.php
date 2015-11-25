<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgreementTemplate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\AgreementTemplateRepository")
 */
class AgreementTemplate
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
     * @var string
     *
     * @ORM\Column(name="agreement", type="text")
     */
    private $agreement;

    /**
     * @var string
     *
     * @ORM\Column(name="signee", type="string", length=255)
     */
    private $signee;

    /**
     * @var string
     *
     * @ORM\Column(name="signee_title", type="string", length=255)
     */
    private $signeeTitle;

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
     * @return AgreementTemplate
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
     * Set agreement
     *
     * @param string $agreement
     * @return AgreementTemplate
     */
    public function setAgreement($agreement)
    {
        $this->agreement = $agreement;

        return $this;
    }

    /**
     * Get agreement
     *
     * @return string 
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * Set signee
     *
     * @param string $signee
     * @return AgreementTemplate
     */
    public function setSignee($signee)
    {
        $this->signee = $signee;

        return $this;
    }

    /**
     * Get signee
     *
     * @return string 
     */
    public function getSignee()
    {
        return $this->signee;
    }

    /**
     * Set signeeTitle
     *
     * @param string $signeeTitle
     * @return AgreementTemplate
     */
    public function setSigneeTitle($signeeTitle)
    {
        $this->signeeTitle = $signeeTitle;

        return $this;
    }

    /**
     * Get signeeTitle
     *
     * @return string 
     */
    public function getSigneeTitle()
    {
        return $this->signeeTitle;
    }

}
