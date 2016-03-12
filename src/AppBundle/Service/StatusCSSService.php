<?php

namespace AppBundle\Service;

class StatusCSSService
{

    protected $statusCSS;

    public function __construct()
    {
        $this->statusCSS = array(
            'backoffice' => 'info',
            'document' => 'warning',
            'post' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            'refunded' => 'danger',
            'cancelled' => 'danger',
            'paid' => 'success',
            'not_paid' => 'danger',
        );
    }

    public function getCSS($key)
    {
        if (key_exists($key, $this->statusCSS))
            return $this->statusCSS[$key];
        else {
            return "default";
        }
    }

}
