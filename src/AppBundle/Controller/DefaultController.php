<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Curl\Curl;

class DefaultController extends Controller
{

  /**
   * @Route("/", name="homepage")
   */
  public function indexAction(Request $request)
  {
    // replace this example code with whatever you need
    return $this->render('default/index.html.twig', array(
          'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
    ));
  }

  /**
   * @Route("/test", name="testModx")
   */
  public function testModxAction()
  {
    $curl = new Curl();
    $curl->get('http://uaevisa.ru/api/v1/orders', array(
      'login' => 'api',
      'pass' => 'YrrLeqhb',
      'startdate' => '2016-09-20 10:00',
      'limit' => '5'
    ));
    $response = $curl;
    $curl->close();

    return $this->render('default/test.html.twig', array('response' => $response));
  }

}
