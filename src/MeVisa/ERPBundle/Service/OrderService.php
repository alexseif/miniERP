<?php

namespace MeVisa\ERPBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class OrderService
{

    protected $em;
    protected $templating;

    public function __construct(EntityManager $em, EngineInterface $templating)
    {
        $this->em = $em;
        $this->templating = $templating;
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
        // TODO: State machine
        // $order->setState('backoffice');
        $order->setChannel('POS');
        $order->setCreatedAt(new \DateTime("now"));
        $order->setNumber($this->generateNewPOSNumber());

        $customer = $order->getCustomer();
        if (!$customer->getId()) {
            $this->em->persist($customer);
            // TODO: Check new Customer
            // TODO: add new customer
        }
        // $customerCheck = $em->getRepository('MeVisaCRMBundle:Customer')->find($order->getCustomer()->getId());
        $customerCheck = true;
        if (!$customerCheck) {
            //  echo "Still no Customer <br/>";
        }
        $this->setOrderDetails($order);
        $this->em->persist($order);
        $this->em->flush();
    }

    public function saveOrder($order)
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

    public function setOrderDetails($order)
    {
        if ("approved" == $order->getState() || "rejected" == $order->getState()) {
            $order->setCompletedAt(new \DateTime());
        }

        if ("post" == $order->getState()) {
            $order->setPostedAt(new \DateTime());
        }

        $orderCompanions = $order->getOrderCompanions();
        // TODO: Check Order Companions
        foreach ($orderCompanions as $companion) {
            if (empty($companion->getId())) {
                $order->addOrderCompanion($companion);
            }
        }

        $orderProducts = $order->getOrderProducts();
        foreach ($orderProducts as $orderProduct) {
            // TODO: Check Order Product
            // TODO: Handle no proper products or disabled
            if (empty($orderProduct->getId())) {
                $order->addOrderProduct($orderProduct);
            }
        }

        $orderComments = $order->getOrderComments();
        foreach ($orderComments as $comment) {
            if (!$comment->getId()) {
                if ("" == $comment->getComment()) {
                    $order->removeOrderComment($comment);
                } else {
                    $this->getUser()->addComment($comment);
                    $comment->setCreatedAt(new \DateTime());
                    if (empty($comment->getId())) {
                        $order->addOrderComment($comment);
                    }
                }
            }
        }

        $invoices = $order->getInvoices();
        foreach ($invoices as $invoice) {
            if (empty($invoice->getId())) {
                $order->addInvoice($invoice);
            }
        }

        $orderPayments = $order->getOrderPayments();
        foreach ($orderPayments as $payment) {
            if ("paid" == $payment->getState()) {
                $payment->setCreatedAt(new \DateTime());
            }
            if (empty($payment->getId())) {
                $order->addOrderPayment($payment);
            }
        }


        // TODO: Check Order
        // TODO: Upload OrderDocuments then presist
        $orderDocuments = $order->getOrderDocuments();
        foreach ($orderDocuments as $document) {
            if (empty($document->getId())) {
                $order->addOrderDocument($document);
            }
        }
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

}
