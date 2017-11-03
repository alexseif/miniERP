<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Currency;
use AppBundle\Form\CurrencyType;

/**
 * Currency controller.
 *
 * @Route("/admin/currency")
 */
class CurrencyController extends Controller
{

  /**
   * Lists all Currency entities.
   *
   * @Route("/", name="admin_currency")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {

    //List details
    $em = $this->getDoctrine()->getManager();

    $entities = $em->getRepository('AppBundle:Currency')->findBy(array(), array('createdAt' => 'Desc'), 7);

    return array(
      'entities' => $entities,
    );
  }

}
