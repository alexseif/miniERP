<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityRepository;

/**
 * Description of CurrencyService
 *
 * @author alexseif
 */
class CurrencyService
{

    public function __construct()
    {
        // TODO: Load Currency Repository, then setup a service to load currency in menu in admin.html.twig
        return $this->getDoctrine()
                        ->getRepository('AppBundle:Currency')
                        ->getToDate();
    }

}
