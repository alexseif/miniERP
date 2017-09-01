<?php

namespace AppBundle\Utils;

/*
 *  The following content was designed & implemented under AlexSeif.com
 */

/**
 * Description of State
 *
 * @author alexseif
 */
class State
{

  protected $key;
  protected $name;
  protected $children;
  protected $bootstrapClass;

  public function __construct($key, $name, $bootstrapClass = "default")
  {
    $this->setKey($key);
    $this->setName($name);
    $this->setBootstrapClass($bootstrapClass);
  }

  function addChild(State $state)
  {
    //TODO: maybe add $key as array index

    $this->children[] = $state;
  }

  function getKey()
  {
    return $this->key;
  }

  function getName()
  {
    return $this->name;
  }

  function getChildren()
  {
    return $this->children;
  }

  function setKey($key)
  {
    $this->key = $key;
  }

  function setName($name)
  {
    $this->name = $name;
  }

  function setChildren($children)
  {
    $this->children = $children;
  }

  function setBootstrapClass($bootstrapClass)
  {
    $this->bootstrapClass = $bootstrapClass;
  }

  function getBootstrapClass()
  {
    return $this->bootstrapClass;
  }

}
