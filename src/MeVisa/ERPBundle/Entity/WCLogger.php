<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WCLogger
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\WCLoggerRepository")
 */
class WCLogger
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
     * @ORM\Column(name="header", type="text")
     */
    private $header;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     * 
     * @ORM\Column(name="result", type="string", length=255, nullable=true)
     */
    private $result;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    function __construct()
    {
        $this->setCreatedAt(new \DateTime());
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
     * Set header
     *
     * @param string $header
     * @return WCLogger
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string 
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return WCLogger
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    function getResult()
    {
        return $this->result;
    }

    function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return WCLogger
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

}
