<?php

namespace MeVisa\ERPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WC
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="MeVisa\ERPBundle\Entity\WCRepository")
 */
class WC
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
     * @ORM\Column(name="get", type="text", nullable=true)
     */
    private $get;

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="text", nullable=true)
     */
    private $post;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * Constructor
     */
    public function __construct()
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
     * Set get
     *
     * @param string $get
     * @return WC
     */
    public function setGet($get)
    {
        $this->get = $get;

        return $this;
    }

    /**
     * Get get
     *
     * @return string 
     */
    public function getGet()
    {
        return $this->get;
    }

    /**
     * Set post
     *
     * @param string $post
     * @return WC
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return string 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return WC
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
