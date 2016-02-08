<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of CurrencyRepository
 *
 * @author alexseif
 */
class CurrencyRepository extends EntityRepository
{

    public function getToDate()
    {
        return $this->getEntityManager()
                        ->getRepository("AppBundle:Currency")
                        ->findOneBy(array(), array("createdAt" => "Desc"));
    }

}
