<?php

namespace Admin\AdminBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="MeVisa\ERPBundle\Entity\OrderComments", mappedBy="author", cascade={"persist"})
     * */
    protected $comments;

    public function __construct()
    {
        parent::__construct();
// your own logic
    }

    /**
     * Add comments
     *
     * @param \MeVisa\ERPBundle\Entity\OrderComments $comments
     * @return User
     */
    public function addComment(\MeVisa\ERPBundle\Entity\OrderComments $comments)
    {
        $this->comments[] = $comments;
        $comments->setAuthor($this);

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \MeVisa\ERPBundle\Entity\OrderComments $comments
     */
    public function removeComment(\MeVisa\ERPBundle\Entity\OrderComments $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

}
