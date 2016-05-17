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
        $em->remove($message);
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
            $em->remove($message);
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
//        if (!$orders) {
//            throw $this->createNotFoundException('Unable to find Reports entity.');
//        }

        return array(
            'os' => $orders,
        );
    }
 /**
     * Finds and displays a Reports entity.
     *
     * @Route("/products", name="reports_products")
     * @Method("GET")
     * @Template()
     */
    public function productsReportAction()
    {
//TODO: Validate get
        $em = $this->getDoctrine()->getManager();

        $orderProducts = $em->getRepository('MeVisaERPBundle:OrderProducts')->findRevenue();
//        if (!$orders) {
//            throw $this->createNotFoundException('Unable to find Reports entity.');
//        }

        return array(
            'ops' => $orderProducts,
        );
        //TODO: pie chart per month for product income
    }
}