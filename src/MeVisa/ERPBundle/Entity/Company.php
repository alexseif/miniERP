<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\CompanyRepository")
 */
class Company
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
     * @ORM\Column(name="address", type="text")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="account", type="text")
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="phones", type="text")
     */
    private $phones;

    /**
     * @var string
     *
     * @ORM\Column(name="signee_1", type="string", length=255)
     */
    private $signee1;

    /**
     * @var string
     *
     * @ORM\Column(name="signee_1_title", type="string", length=255)
     */
    private $signee1Title;

    /**
     * @var string
     *
     * @ORM\Column(name="signee_2", type="string", length=255)
     */
    private $signee2;

    /**
     * @var string
     *
     * @ORM\Column(name="signee_2_title", type="string", length=255)
     */
    private $signee2Title;

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
     * @return Company
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
     * Set address
     *
     * @param string $address
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set account
     *
     * @param string $account
     * @return Company
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return string 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set phones
     *
     * @param string $phones
     * @return Company
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return string 
     */
    public function getPhones()
    {
        return $this->phones;
    }


    /**
     * Set signee1
     *
     * @param string $signee1
     * @return Company
     */
    public function setSignee1($signee1)
    {
        $this->signee1 = $signee1;

        return $this;
    }

    /**
     * Get signee1
     *
     * @return string 
     */
    public function getSignee1()
    {
        return $this->signee1;
    }

    /**
     * Set signee1Title
     *
     * @param string $signee1Title
     * @return Company
     */
    public function setSignee1Title($signee1Title)
    {
        $this->signee1Title = $signee1Title;

        return $this;
    }

    /**
     * Get signee1Title
     *
     * @return string 
     */
    public function getSignee1Title()
    {
        return $this->signee1Title;
    }

    /**
     * Set signee2
     *
     * @param string $signee2
     * @return Company
     */
    public function setSignee2($signee2)
    {
        $this->signee2 = $signee2;

        return $this;
    }

    /**
     * Get signee2
     *
     * @return string 
     */
    public function getSignee2()
    {
        return $this->signee2;
    }

    /**
     * Set signee2Title
     *
     * @param string $signee2Title
     * @return Company
     */
    public function setSignee2Title($signee2Title)
    {
        $this->signee2Title = $signee2Title;

        return $this;
    }

    /**
     * Get signee2Title
     *
     * @return string 
     */
    public function getSignee2Title()
    {
        return $this->signee2Title;
    }
}
