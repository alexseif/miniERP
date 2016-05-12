<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Reports controller.
 *
 * @Route("/reports")
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
        return array();
    }

    /**
     * Lists all Accoungting Reports
     *
     * @Route("/accounting", name="reports_accounting")
     * @Method("GET")
     * @Template()
     */
    public function accountingAction()
    {
        $em = $this->getDoctrine()->getManager();

        $reports = $em->getRepository('MeVisaERPBundle:Orders')->findAllGroupByMonthAndYear();

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

        $orders = $em->getRepository('MeVisaERPBundle:Orders')->findByMonthAndYear($month, $year);
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
     * @Route("/products",  name="reports_products_cost")
     * @Method("GET")
     * @Template()
     */
    public function productsReportAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orderProducts = $em->getRepository('MeVisaERPBundle:OrderProducts')->findWithMessages();
       
//TODO: make order edit form for order products
        return array(
            'orderProducts' => $orderProducts,
        );
    }

}
