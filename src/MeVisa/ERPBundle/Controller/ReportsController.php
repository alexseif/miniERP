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
        $em = $this->getDoctrine()->getManager();

        $reports = $em->getRepository('MeVisaERPBundle:Orders')->findAllGroupByMonthAndYear();

        return array(
            'reports' => $reports,
        );
    }

    /**
     * Finds and displays a Reports entity.
     *
     * @Route("/{year}/{month}", name="reports_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($month, $year)
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

}
