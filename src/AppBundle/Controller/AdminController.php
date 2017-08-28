<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Gedmo\Loggable\Entity\LogEntry;
 /**
   * @Route("/admin")
  * 
  */
class AdminController extends Controller
{

  /**
   * @Route("/", name="dashboard")
   * @Template()
   */
  public function dashboardAction()
  {
    $em = $this->getDoctrine()->getManager();

    $backoffice = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("backoffice");
    $document = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("document");
    $post = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("post");
    $completed = $em->getRepository('MeVisaERPBundle:Orders')->findAllComplete();
    $notPaid = $em->getRepository('MeVisaERPBundle:Orders')->findAllNotPaid();

    return array(
      "backoffice" => $backoffice,
      "document" => $document,
      "post" => $post,
      "completed" => $completed,
      "not_paid" => $notPaid,
    );
  }

  /**
   * @Route("/ajax", name="dashboard_ajax")
   * @Template()
   */
  public function dashboardAjaxAction()
  {
    $em = $this->getDoctrine()->getManager();

//        $pending = $em->getRepository('MeVisaERPBundle:Orders')->findAllPending("pending");
    $backoffice = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("backoffice");
    $document = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("document");
    $post = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("post");
    $completed = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("approved");

//        return new JsonResponse(array(
//            "pending" => $pending,
//            "backoffice" => $backoffice,
//            "document" => $document,
//            "post" => $post,
//            "completed" => $completed,
//        ));
    return array(
//            "pending" => $pending,
      "backoffice" => $backoffice,
      "document" => $document,
      "post" => $post,
      "completed" => $completed,
    );
  }

  /**
   * @Route("/search", name="search")
   * @Template()
   */
  public function searchAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $results = $em->getRepository('MeVisaERPBundle:Orders')->searchQuery($request->get('search'));

    return array(
      "results" => $results
    );
  }

  /**
   * @Route("/send", name="send")
   * @Template()
   */
  public function sendAction(Request $request)
  {
    $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('zakaz@mevisa.ru')
        ->setTo('alex.seif@gmail.com')
        ->setBody('You should see me from the profiler!')
    ;

    $this->get('mailer')->send($message);

    return array();
  }

  /**
   * @Route("/newsfeed", name="newsfeed")
   * @Template()
   */
  public function newsfeedAction(Request $request)
  {
    //TODO: Only select today's items
    $em = $this->getDoctrine()->getManager();
    $em->getRepository("Gedmo\Loggable\Entity\LogEntry");
    $qr = $em->createQueryBuilder();
    $logQuery = $qr->select("ele")
        ->from("Gedmo\Loggable\Entity\LogEntry", "ele")
        ->where("ele.objectClass = 'MeVisa\\ERPBundle\\Entity\\Products'")
        ->orWhere("ele.objectClass = 'MeVisa\\ERPBundle\\Entity\\ProductPrices'")
        ->orderBy("ele.id", "DESC");
    $logResults = $logQuery->getQuery()
        ->getResult();

    foreach ($logResults as $key => $logRow) {
      $logResults[$key]->object = $em->getRepository($logRow->getObjectClass())->find($logRow->getObjectId());
      $on = explode('\\', $logRow->getObjectClass());
      $on = end($on);
      switch ($on) {
        case 'Products':
          $logResults[$key]->product = $logResults[$key]->object;
          $logResults[$key]->objectName = 'Product : ' . $logResults[$key]->object->getName();
          break;
        case 'ProductPrices':
          $logResults[$key]->product = $logResults[$key]->object->getProduct();
          $logResults[$key]->objectName = 'Price for : ' . $logResults[$key]->object->getProduct()->getName();
          $logResults[$key]->setData(array(
            "cost" => $logResults[$key]->object->getCost() / 100,
            "price" => $logResults[$key]->object->getPrice() / 100
          ));
          break;
      }
    }
    return array('log' => $logResults);
  }

}
