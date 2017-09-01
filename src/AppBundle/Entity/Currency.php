<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CurrencyRepository")
 */
class Currency
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
   * @ORM\Column(name="cur1", type="string", length=4)
   */
  private $cur1;

  /**
   * @var string
   *
   * @ORM\Column(name="cur2", type="string", length=4)
   */
  private $cur2;

  /**
   * @var integer
   *
   * @ORM\Column(name="value", type="integer")
   */
  private $value;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createdAt", type="datetime")
   */
  private $createdAt;

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
   * Set cur1
   *
   * @param string $cur1
   * @return Currency
   */
  public function setCur1($cur1)
  {
    $this->cur1 = $cur1;

    return $this;
  }

  /**
   * Get cur1
   *
   * @return string 
   */
  public function getCur1()
  {
    return $this->cur1;
  }

  /**
   * Set cur2
   *
   * @param string $cur2
   * @return Currency
   */
  public function setCur2($cur2)
  {
    $this->cur2 = $cur2;

    return $this;
  }

  /**
   * Get cur2
   *
   * @return string 
   */
  public function getCur2()
  {
    return $this->cur2;
  }

  /**
   * Set value
   *
   * @param integer $value
   * @return Currency
   */
  public function setValue($value)
  {
    $this->value = $value;

    return $this;
  }

  /**
   * Get value
   *
   * @return integer 
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Set createdAt
   *
   * @param \DateTime $createdAt
   * @return Currency
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
