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
        $this->addState('not_paid', 'Not Paid', 'danger');
        $this->addState('paid', 'Paid', 'success');
        $this->addState('document', 'Document', 'warning');
        $this->addState('post', 'Post', 'success');
        $this->addState('approved', 'Approved', 'success');
        $this->addState('rejected', 'Rejected', 'default');

        $this->addChild('backoffice', 'not_paid');
        $this->addChild('backoffice', 'document');
        $this->addChild('backoffice', 'post');

        $this->addChild('not_paid', 'paid');
        $this->addChild('not_paid', 'document');
        $this->addChild('not_paid', 'post');

        $this->addChild('document', 'post');

        $this->addChild('post', 'approved');
        $this->addChild('post', 'rejected');

        //WooCommerce Status
        //Pending
        $this->addState('pending', 'Pending');
        $this->addChild('pending', 'backoffice');

        //Processing
        $this->addState('processing', 'Processing');
        $this->addChild('processing', 'backoffice');
        //Cancelled
        $this->addState('cancelled', 'Cancelled');
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

}
