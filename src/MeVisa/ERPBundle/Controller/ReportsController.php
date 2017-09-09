<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use MeVisa\ERPBundle\Form\OrderProductsFixType;

/**
 * Reports controller.
 *
 * @Route("/reports")
 * @Security("has_role('ROLE_ACCOUNTANT')")
 */
class ReportsController extends Controller
{

  /**
   * Lists all Reports
   *
   * @Route("/", name="reports")

   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    // is_granted('ROLE_ACCOUNTANT');
    return array();
  }

  /**
   * Lists all Financial Reports
   *
   * @Route("/financial", name="reports_financial")
   * @Method("GET")
   * @Template()
   */
  public function financialAction()
  {
    $reports = $this->getAllGroupByMonthAndYear();
    return array(
      'reports' => $reports,
    );
  }

  /**
   * Lists all Financial Reports
   *
   * @Route("/financial/archive", name="reports_financial_archive")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Reports:financial.html.twig")
   */
  public function financialArchiveAction()
  {
    $reports = $this->getArchiveGroupByMonthAndYear();
    return array(
      'reports' => $reports,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/financial/{year}/{month}", name="reports_financial_show")
   * @Method("GET")
   * @Template()
   */
  public function financialReportAction($month, $year)
  {
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();

    $orders = $em->getRepository('MeVisaERPBundle:Orders')->findByMonthAndYear($month, $year);

    return array(
      'month' => $month,
      'year' => $year,
      'orders' => $orders,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/financial/custom", name="reports_financial_custom")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Reports:financialReport.html.twig")
   */
  public function financialCustomReportAction(Request $request)
  {
    $from = $request->query->get('from');
    $to = $request->query->get('to');
    $from = explode('.', $from);
    $from = $from[2] . '-' . $from[1] . '-' . $from[0];
    $to = explode('.', $to);
    $to = $to[2] . '-' . $to[1] . '-' . $to[0];
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();

    $orders = $em->getRepository('MeVisaERPBundle:Orders')->findByFromAndTo($from, $to);

    return array(
      'month' => $from,
      'year' => $to,
      'orders' => $orders,
    );
  }

  /**
   * Lists all Accounting Reports
   *
   * @Route("/accounting", name="reports_accounting")
   * @Method("GET")
   * @Template()
   */
  public function accountingAction()
  {
    $reports = $this->getAllGroupByMonthAndYear();

    return array(
      'reports' => $reports,
    );
  }

  /**
   * Lists all Accounting Reports
   *
   * @Route("/accounting/archive", name="reports_accounting_archive")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Reports:accounting.html.twig")
   */
  public function accountingArchiveAction()
  {
    $reports = $this->getArchiveGroupByMonthAndYear();

    return array(
      'reports' => $reports,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/accounting/{year}/{month}", name="reports_accounting_show")
   * @Method("GET")
   * @Template()
   */
  public function accountingReportAction($month, $year)
  {
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();

    $orders = $em->getRepository('MeVisaERPBundle:Orders')->findByMonthAndYearNoCash($month, $year);
//        if (!$orders) {
//            throw $this->createNotFoundException('Unable to find Reports entity.');
//        }

    return array(
      'month' => $month,
      'year' => $year,
      'orders' => $orders,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/accounting/custom", name="reports_accounting_custom")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Reports:accountingReport.html.twig")
   */
  public function accountingCustomReportAction(Request $request)
  {
    $from = $request->query->get('from');
    $to = $request->query->get('to');
    $from = explode('.', $from);
    $from = $from[2] . '-' . $from[1] . '-' . $from[0];
    $to = explode('.', $to);
    $to = $to[2] . '-' . $to[1] . '-' . $to[0];
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();

    $orders = $em->getRepository('MeVisaERPBundle:Orders')->findByFromAndToNoCash($from, $to);

    return array(
      'month' => $from,
      'year' => $to,
      'orders' => $orders,
    );
  }

  /**
   * Lists all Reports
   *
   * @Route("/vendors", name="reports_vendors")
   * @Method("GET")
   * @Template()
   */
  public function vendorsAction()
  {
    $em = $this->getDoctrine()->getManager();

    $reports = $em->getRepository('MeVisaERPBundle:Orders')->findAllGroupByMonthAndYearAndVendor();

    return array(
      'reports' => $reports,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/vendors/{year}/{month}/{vendor_id}",  defaults={"vendor_id" = null}, name="reports_vendors_show")
   * @Method("GET")
   * @Template()
   */
  public function vendorsReportAction($month, $year, $vendor_id)
  {
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();
    $vendor = null;
    if ($vendor_id) {
      $vendor = $em->getRepository('MeVisaERPBundle:Vendors')->find($vendor_id);
      $orders = $em->getRepository('MeVisaERPBundle:Orders')->findByMonthAndYearAndVendor($month, $year, $vendor_id);
    } else {
      $orders = $em->getRepository('MeVisaERPBundle:Orders')->findByMonthAndYearAndNoVendor($month, $year);
    }
//        if (!$orders) {
//            throw $this->createNotFoundException('Unable to find Reports entity.');
//        }

    return array(
      'vendor' => $vendor,
      'month' => $month,
      'year' => $year,
      'orders' => $orders,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/problems",  name="reports_problems")
   * @Method("GET")
   * @Template()
   */
  public function problemsReportAction()
  {
    $em = $this->getDoctrine()->getManager();

    $orderProducts = $em->getRepository('MeVisaERPBundle:OrderProducts')->findWithMessages();

    return array(
      'orderProducts' => $orderProducts,
    );
  }

  /**
   * Displays a form to fix an existing Orders entity.
   *
   * @Route("/problems/{id}/edit", name="reports_problems_fix")
   * @Method({"GET", "PUT"})
   * @Template()
   */
  public function problemsReportFormAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $order = $this->get('erp.order')->getOrder($id);
    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $form = $this->createForm(new OrderProductsFixType(), $order, array(
      'action' => $this->generateUrl('reports_problems_fix', array('id' => $order->getId())),
      'method' => 'PUT',
    ));

    $form->add('update', 'submit', array(
      'label' => null,
      'attr' => array(
        'class' => 'btn-success pull-right'
    )));
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $this->get('erp.order')->updateOrder($order);

        $messages = $em->getRepository('MeVisaERPBundle:OrderMessages')->findBy(array("orderRef" => $order->getId()));
        if ($messages) {
          foreach ($messages as $message) {
            $em->remove($message);
          }
          $em->flush();
        }
        return $this->redirect($this->generateUrl('reports_problems'));
      }
    }

    return array(
      'order' => $order,
      'productPrices' => $em->getRepository('MeVisaERPBundle:ProductPrices')->findAllPrices(),
      'form' => $form->createView(),
    );
  }

  /**
   * Displays a form to fix an existing Orders entity.
   *
   * @Route("/problems/{id}/delete", name="reports_problems_delete")
   * @Method("DELETE")
   */
  public function deleteMessage($id)
  {
    $em = $this->getDoctrine()->getManager();
    $message = $em->getRepository('MeVisaERPBundle:OrderMessages')->find($id);
    if (!$message) {
      throw $this->createNotFoundException('Unable to find Message.');
    }
    $message->setRead(TRUE);
    $em->flush();

    return new Response();
  }

  /**
   * Displays a form to fix an existing Orders entity.
   *
   * @Route("/problems/{id}/deleteall", name="reports_problems_delete_all")
   * @Method("DELETE")
   */
  public function deleteAllMessage($id)
  {
    $em = $this->getDoctrine()->getManager();
    $messages = $em->getRepository('MeVisaERPBundle:OrderMessages')->findBy(array("orderProduct" => $id));
    if (!$messages) {
      throw $this->createNotFoundException('Unable to find Message.');
    }
    foreach ($messages as $message) {
      $message->setRead(TRUE);
    }
    $em->flush();

    return new Response();
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/revenue", name="reports_revenue")
   * @Method("GET")
   * @Template()
   */
  public function revenueReportAction()
  {
    //TODO: Validate get
    $em = $this->getDoctrine()->getManager();

    $orders = $em->getRepository('MeVisaERPBundle:Orders')->findRevenue();

    return array(
      'os' => $orders,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/products/{year}/{month}", defaults={"year" = null, "month" = null}, name="reports_products")
   * @Method("GET")
   * @Template()
   */
  public function productsReportAction($year, $month)
  {
    //TODO: Validate get
    $em = $this->getDoctrine()->getManager();
    $reports = $this->getAllGroupByMonthAndYear();

    if (is_null($year) || is_null($month)) {
      $orderProducts = $em->getRepository('MeVisaERPBundle:OrderProducts')->findRevenue();
    } else {
      $orderProducts = $em->getRepository('MeVisaERPBundle:OrderProducts')->findRevenueByMonthAndYear($month, $year);
    }

    return array(
      'ops' => $orderProducts,
      'reports' => $reports,
      'year' => $year,
      'month' => $month
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/employees/{year}/{month}", defaults={"year" = null, "month" = null}, name="reports_employees")
   * @Method("GET")
   * @Template()
   */
  public function employeesReportAction($year, $month)
  {
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();
    $reports = $this->getAllGroupByMonthAndYear();
    $logRepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');

    if (is_null($year) || is_null($month)) {
      $userLog = $logRepo->createQueryBuilder('ele')
          ->select('ele, COUNT(ele.username) as cUpdates, ele.username')
          ->groupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->orderBy('cUpdates', 'DESC')
          ->getQuery()
          ->getResult();

      $logs = $logRepo->createQueryBuilder('ele')
          ->select('ele.username, ele.loggedAt, '
              . 'DATE(ele.loggedAt) as dLoggedAt, '
              . 'COUNT(ele.loggedAt) as cUpdates, '
              . 'TIMEDIFF(MAX(TIME(ele.loggedAt)),MIN(TIME(ele.loggedAt))) as timespan'
          )
          ->groupBy("dLoggedAt")
          ->addGroupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->orderBy('dLoggedAt')
          ->getQuery()
          ->getResult();
    } else {
      $userLog = $logRepo->createQueryBuilder('ele')
          ->select('ele, COUNT(ele.username) as cUpdates, ele.username')
          ->groupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->andWhere('MONTHNAME(ele.loggedAt) = ?1')
          ->andWhere('YEAR(ele.loggedAt) = ?2')
          ->setParameter('1', $month)
          ->setParameter('2', $year)
          ->orderBy('cUpdates', 'DESC')
          ->getQuery()
          ->getResult();

      $logs = $logRepo->createQueryBuilder('ele')
          ->select('ele.username, ele.loggedAt, '
              . 'DATE(ele.loggedAt) as dLoggedAt, '
              . 'COUNT(ele.loggedAt) as cUpdates, '
              . 'TIMEDIFF(MAX(TIME(ele.loggedAt)),MIN(TIME(ele.loggedAt))) as timespan'
          )
          ->groupBy("dLoggedAt")
          ->addGroupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->andWhere('MONTHNAME(ele.loggedAt) = ?1')
          ->andWhere('YEAR(ele.loggedAt) = ?2')
          ->setParameter('1', $month)
          ->setParameter('2', $year)
          ->orderBy('dLoggedAt')
          ->getQuery()
          ->getResult();
    }
    $users = $em->getRepository('AppBundle:User')->findAll();


    return array(
      'logs' => $logs,
      'users' => $users,
      'userLog' => $userLog,
      'reports' => $reports,
      'year' => $year,
      'month' => $month
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/employee/{username}/{year}/{month}", defaults={"year" = null, "month" = null}, name="reports_employee")
   * @Method("GET")
   * @Template()
   */
  public function employeeReportAction($username, $year, $month)
  {
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();
    $reports = $this->getAllGroupByMonthAndYear();
    $logRepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');

    if (is_null($year) || is_null($month)) {
      $userLog = $logRepo->createQueryBuilder('ele')
          ->select('ele, COUNT(ele.username) as cUpdates, ele.username')
          ->groupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->orderBy('cUpdates', 'DESC')
          ->getQuery()
          ->getResult();

      $logs = $logRepo->createQueryBuilder('ele')
          ->select('ele.username, ele.loggedAt, '
              . 'DATE(ele.loggedAt) as dLoggedAt, '
              . 'COUNT(ele.loggedAt) as cUpdates, '
              . 'TIMEDIFF(MAX(TIME(ele.loggedAt)),MIN(TIME(ele.loggedAt))) as timespan'
          )
          ->groupBy("dLoggedAt")
          ->addGroupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->orderBy('dLoggedAt')
          ->getQuery()
          ->getResult();
    } else {
      $userLog = $logRepo->createQueryBuilder('ele')
          ->select('ele, COUNT(ele.username) as cUpdates, ele.username')
          ->groupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->andWhere('MONTHNAME(ele.loggedAt) = ?1')
          ->andWhere('YEAR(ele.loggedAt) = ?2')
          ->setParameter('1', $month)
          ->setParameter('2', $year)
          ->orderBy('cUpdates', 'DESC')
          ->getQuery()
          ->getResult();

      $logs = $logRepo->createQueryBuilder('ele')
          ->select('ele.username, ele.loggedAt, '
              . 'DATE(ele.loggedAt) as dLoggedAt, '
              . 'COUNT(ele.loggedAt) as cUpdates, '
              . 'TIMEDIFF(MAX(TIME(ele.loggedAt)),MIN(TIME(ele.loggedAt))) as timespan'
          )
          ->groupBy("dLoggedAt")
          ->addGroupBy("ele.username")
          ->where('ele.username IS NOT NULL')
          ->andWhere('MONTHNAME(ele.loggedAt) = ?1')
          ->andWhere('YEAR(ele.loggedAt) = ?2')
          ->setParameter('1', $month)
          ->setParameter('2', $year)
          ->orderBy('dLoggedAt')
          ->getQuery()
          ->getResult();
    }
    $users = $em->getRepository('AppBundle:User')->findAll();


    return array(
      'logs' => $logs,
      'users' => $users,
      'userLog' => $userLog,
      'reports' => $reports,
      'year' => $year,
      'month' => $month
    );
  }

  /**
   * Lists all Financial Reports
   *
   * @Route("/salesby", name="reports_sales_by")
   * @Method("GET")
   * @Template()
   */
  public function salesByAction()
  {
    $reports = $this->getAllGroupByMonthAndYear();

    return array(
      'reports' => $reports,
    );
  }

  /**
   * Finds and displays a Reports entity.
   *
   * @Route("/salesby/{year}/{month}", name="reports_sales_by_show")
   * @Method("GET")
   * @Template()
   */
  public function salesByReportAction($month, $year)
  {
//TODO: Validate get
    $em = $this->getDoctrine()->getManager();
    $ordersReport = $em->getRepository('MeVisaERPBundle:Orders');
    $salesByUser = $ordersReport->createQueryBuilder('o')
        ->select('o, COUNT(o.salesBy) as sales')
        ->groupBy("o.salesBy")
        ->where('o.salesBy IS NOT NULL')
        ->andWhere('MONTHNAME(o.createdAt) = ?1')
        ->andWhere('YEAR(o.createdAt) = ?2')
        ->setParameter('1', $month)
        ->setParameter('2', $year)
        ->orderBy('sales', 'DESC')
        ->getQuery()
        ->getResult();

    return array(
      'month' => $month,
      'year' => $year,
      'salesByUser' => $salesByUser,
    );
  }

  public function getAllGroupByMonthAndYear()
  {
    $em = $this->getDoctrine()->getManager();
    return $em->getRepository('MeVisaERPBundle:Orders')->findAllGroupByMonthAndYear();
  }

  public function getArchiveGroupByMonthAndYear()
  {
    $em = $this->getDoctrine()->getManager();
    return $em->getRepository('MeVisaERPBundle:Orders')->findArchiveGroupByMonthAndYear();
  }

}
