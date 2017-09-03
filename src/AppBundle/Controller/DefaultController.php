<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Curl\Curl;

class DefaultController extends Controller
{

  /**
   * @Route("/", name="homepage")
   */
  public function indexAction(Request $request)
  {
    return $this->render('default/index.html.twig', array(
          'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
    ));
  }

  /**
   * @Route("/metronic", name="metronic")
   * @Template("AppBundle:metronic:dashboard.html.twig")
   */
  public function metronicAction()
  {
    return array();
  }

  /**
   * @Route("/form_controls", name="form-controls")
   * @Template("AppBundle:metronic:form_controls.html.twig")
   */
  public function formControlsActions()
  {
    return array();
  }
}
