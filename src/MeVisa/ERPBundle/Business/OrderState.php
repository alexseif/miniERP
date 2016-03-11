<?php

namespace MeVisa\ERPBundle\Business;

use AppBundle\Utils\State;

/**
 * Description of OrderState
 *
 * @author alexseif
 */
class OrderState
{

    protected $states;
    protected $currentState;

    function __construct()
    {
        $this->states = array();

        $this->addState('backoffice', 'Back Office', 'info');
        $this->addState('document', 'Document', 'warning');
        $this->addState('post', 'Post', 'primary');
        $this->addState('approved', 'Approved', 'success');
        $this->addState('rejected', 'Rejected', 'danger');
//        $this->addState('refunded', 'Refunded', 'danger');
        $this->addState('cancelled', 'Cancelled', 'danger');

        $this->addChild('backoffice', 'document');
        $this->addChild('document', 'backoffice');
        $this->addChild('backoffice', 'post');
        $this->addChild('post', 'backoffice');
        $this->addChild('backoffice', 'cancelled');
        $this->addChild('cancelled', 'backoffice');
//        $this->addChild('backoffice', 'refunded');
//        $this->addChild('refunded', 'backoffice');

        $this->addChild('document', 'post');
        $this->addChild('post', 'document');
        $this->addChild('document', 'cancelled');
        $this->addChild('cancelled', 'document');

        $this->addChild('post', 'approved');
        $this->addChild('approved', 'post');
        $this->addChild('post', 'rejected');
        $this->addChild('rejected', 'post');
    }

    public function setState($key)
    {

        if (!array_key_exists($key, $this->states)) {
            // FIXME: State not found
        }
        if ($this->currentState)
            if (!array_key_exists($key, $this->currentState->getChildren())) {
                // FIXME: Illegal transition
            }
        $this->currentState = $this->states[$key];
    }

    public function getCurrentState()
    {
        return $this->currentState;
    }

    public function getAvailableStates()
    {
        return $this->currentState->getChildren();
    }

    public function addState($key, $name, $bootstrapClass = "default")
    {
        $this->states[$key] = new State($key, $name, $bootstrapClass);
    }

    public function getState($key)
    {
        return $this->states[$key];
    }

    public function getName($key)
    {
        return $this->states[$key]->getName();
    }

    public function addChild($parent, $child)
    {
        //TODO: check duplication and check state exists
        $this->states[$parent]->addChild($this->getState($child));
    }

    public function getStates()
    {
        return $this->states;
    }

}
