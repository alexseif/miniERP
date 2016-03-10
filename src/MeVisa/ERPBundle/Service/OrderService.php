<?php

namespace MeVisa\ERPBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class OrderService
{

    protected $em;
    protected $templating;
    protected $securityContext;

    public function __construct(EntityManager $em, EngineInterface $templating, \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $securityContext)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->securityContext = $securityContext;
    }

    public function getOrdersList()
    {
        return $this->em->getRepository('MeVisaERPBundle:Orders')->findAll();
    }

    public function getOrder($id)
    {
        $order = $this->em->getRepository('MeVisaERPBundle:Orders')->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }
        $state = $order->getState();
        $order->startOrderStateEnginge();
        $order->setOrderState($state);
        return $order;
    }

    public function getOrderLog($id)
    {
        $logRepo = $this->em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $orderLog = $this->em->find('MeVisa\ERPBundle\Entity\Orders', $id);
        return $logRepo->getLogEntries($orderLog);
    }

    public function generateNewPOSNumber()
    {
        $POSNumber = '';
        $lastPOSOrder = $this->em->getRepository('MeVisaERPBundle:Orders')->queryLastPOSOrder();
        $lastPOSNo = ltrim($lastPOSOrder->getNumber(), 'POS');
        if ($lastPOSOrder) {
            $lastPOSNo = ltrim($lastPOSOrder->getNumber(), 'POS');
            $POSNumber = 'POS' . ($lastPOSNo + 1);
        } else {
            $POSNumber = 'POS1';
        }
        return $POSNumber;
    }

    public function createNewPOSOrder($order)
    {
        $order->setNumber($this->generateNewPOSNumber());

        $this->setOrderDetails($order);
        $this->em->persist($order);
        $this->em->flush();
    }

    public function updateOrder($order)
    {
        $this->setOrderDetails($order);
        if (empty($order->getUpdatedAt())) {
            $order->setUpdatedAt(new \DateTime());
        }
        $this->em->flush();
    }

    public function addNewComment($comment)
    {
        
    }

    public function generateInvoice($id)
    {
        $order = $this->getOrder($id);
        // FIXME: invoice id
        $CompanySettings = $this->em->getRepository('MeVisaERPBundle:CompanySettings')->find(1);

        $invoice = new \MeVisa\ERPBundle\Entity\Invoices();
        $invoices = $order->getInvoices();
        foreach ($invoices as $inv) {
            $invoice = $inv;
        }
        $invoice->setCreatedAt(new \DateTime());

        $products = $order->getOrderProducts();
        $productsLine = array();
        foreach ($products as $product) {
            $productsLine[] = $product->getProduct()->getName();
        }
        $productsLine = implode(',', $productsLine);
        $myProjectDirectory = __DIR__ . '/../../../../';
        $invoiceName = 'mevisa-invoice-' . $order->getNumber() . '-' . $invoice->getId() . '.pdf';
        $invoicePath = $myProjectDirectory . 'web/invoices/';
        $pdfInvoiceHTML = $this->templating->render(
                'MeVisaERPBundle:Orders:pdfinvoice.html.twig', array(
            'order' => $order,
            'invoice' => $invoice,
            'companySettings' => $CompanySettings
                )
        );
        $pdfAgreementHTML = $this->templating->render(
                'MeVisaERPBundle:Orders:pdfagreement.html.twig', array(
            'order' => $order,
            'productsLine' => $productsLine,
            'invoice' => $invoice,
            'companySettings' => $CompanySettings
                )
        );
        $pdfWaiverHTML = $this->templating->render(
                'MeVisaERPBundle:Orders:pdfwaiver.html.twig', array(
            'order' => $order,
            'invoice' => $invoice,
            'companySettings' => $CompanySettings
                )
        );
        $mpdf = new \mPDF("ru-RU", "A4");
        $mpdf->SetTitle("MeVisa Invoice " . $order->getNumber() . '-' . $invoice->getId());
        $mpdf->SetAuthor($CompanySettings->getName());
        $mpdf->WriteHTML($pdfInvoiceHTML);
        $mpdf->AddPage();
        $mpdf->WriteHTML($pdfAgreementHTML);
        $mpdf->AddPage();
        $mpdf->WriteHTML($pdfWaiverHTML);
        $mpdf->Output($invoicePath . $invoiceName, 'F');

        $this->em->flush();
    }

    public function setCustomer($order)
    {
        // Customer Exists By ID
        if (empty($order->getCustomer()->getId())) {
            // Customer Exists By Email
            $customer = $this->em->getRepository('MeVisaCRMBundle:Customers')->findOneBy(array("email" => $order->getCustomer()->getEmail()));
            if ($customer) {
                if ($customer->getName() != $order->getCustomer()->getName()) {
                    //TODO: do something
                }
                if ($customer->getPhone() != $order->getCustomer()->getPhone()) {
                    //TODO: do something
                }
                $customer->addOrder($order);
                $order->setCustomer($customer);
            }
        } else {
            $customer = $this->em->getRepository('MeVisaCRMBundle:Customers')->find($order->getCustomer()->getId());
        }
        if (empty($customer)) {
            // New Customer
            $customer = $order->getCustomer();
        }
        $customer->addOrder($order);

        $this->em->persist($customer);
    }

    public function setOrderDetails($order)
    {
        /* Auto assign details */

        if ("approved" == $order->getState() || "rejected" == $order->getState()) {
            $order->setCompletedAt(new \DateTime());
        }

        if ("post" == $order->getState()) {
            $order->setPostedAt(new \DateTime());
        }
//        $wcId;
//        $updatedAt;
//        $postedAt;
//        $deletedAt;
//        $completedAt;

        /* Order Customer */
        $this->setCustomer($order);

        /* Order Details */
        // TODO: State machine
//        $productsTotal;
//        $adjustmentTotal;
//        $total;
//        $people;
//        $arrival;
//        $departure;

        /* Order Products */
        $orderProducts = $order->getOrderProducts();
        foreach ($orderProducts as $orderProduct) {
            // TODO: Check Order Product
            // TODO: Handle no proper products or disabled
            if (empty($orderProduct->getId())) {
                $order->addOrderProduct($orderProduct);
            }
        }

        /* Order Payments */
        $orderPayments = $order->getOrderPayments();
        foreach ($orderPayments as $payment) {
            if ("paid" == $payment->getState()) {
                $payment->setCreatedAt(new \DateTime());
            }
            if (empty($payment->getId())) {
                $order->addOrderPayment($payment);
            }
        }

        /* Order Companions */
        $orderCompanions = $order->getOrderCompanions();
        // TODO: Check Order Companions
        foreach ($orderCompanions as $companion) {
            if (empty($companion->getId())) {
                $order->addOrderCompanion($companion);
            }
        }

        /* Order Docs */
        $orderDocuments = $order->getOrderDocuments();
        foreach ($orderDocuments as $document) {
            if (empty($document->getId())) {
                $order->addOrderDocument($document);
            }
        }

        /* Order Notes */
        $orderComments = $order->getOrderComments();
        foreach ($orderComments as $comment) {
            if (empty($comment->getId())) {
                if (null == $comment->getComment() || "" == trim($comment->getComment())) {
                    $order->removeOrderComment($comment);
                    $this->em->remove($comment);
                } else {
                    $newComment = new \MeVisa\ERPBundle\Entity\OrderComments();
                    $newComment->setCreatedAt(new \DateTime());
                    $newComment->setComment($comment->getComment());
                    $this->securityContext->getToken()->getUser()->addComment($newComment);
                    $order->addOrderComment($newComment);
                }
            } else {
                $this->em->refresh($comment);
            }
        }

        /* Order Invoice */
        $invoices = $order->getInvoices();
        foreach ($invoices as $invoice) {
            if (empty($invoice->getId())) {
                $order->addInvoice($invoice);
            }
        }

        /* Order Receipt */
    }

}
