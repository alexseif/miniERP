<?php

namespace MeVisa\ERPBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class OrderService
{

    protected $em;
    protected $templating;

    /**
     * @InjectParams({
     *    "em" = @Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(EntityManager $em, EngineInterface $templating)
    {
        $this->em = $em;
        $this->templating = $templating;
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

    public function generateInvoice($order)
    {
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

}
