<?php

namespace MeVisa\ERPBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use MeVisa\ERPBundle\WCAPI\RESTAPI;
use MeVisa\ERPBundle\Entity\Orders;
use MeVisa\ERPBundle\Entity\OrderProducts;
use MeVisa\ERPBundle\Entity\OrderPayments;
use MeVisa\ERPBundle\Entity\OrderComments;
use MeVisa\ERPBundle\Entity\Products;
use MeVisa\ERPBundle\Entity\ProductPrices;
use MeVisa\CRMBundle\Entity\Customers;

class WCAPICommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('wcapi:get')
                ->setDescription('Fetch orders from WC');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $client = new RESTAPI();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $wcOrders = $client->getCompletedOrders();
        foreach ($wcOrders['orders'] as $wcOrder) {
            $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('wcId' => $wcOrder['order_number']));
            if (!$order) {
                $order = $this->newOrder($em, $wcOrder, $output);
            }
            $wcOrderNotes = $client->getOrderNotes($wcOrder['order_number']);
//            $this->updateOrderNotes($em, $order, $wcOrderNotes['order_notes']);
//            $em->persist($order);
        }
//        $em->flush();
        $output->writeln('complete');
    }

    public function newOrder($em, $wcOrder, $output)
    {
        $timezone = new \DateTimeZone('UTC');

        $order = new Orders();

        $customer = new Customers();
        $customer->setName($wcOrder['billing_address']['first_name'] . ' ' . $wcOrder['billing_address']['last_name']);
        $customer->setEmail($wcOrder['billing_address']['email']);
        $customer->setPhone($wcOrder['billing_address']['phone']);

        $customerExists = $em->getRepository('MeVisaCRMBundle:Customers')->findOneBy(array('email' => $customer->getEmail()));

        if (!$customerExists) {
            $em->persist($customer);
            $order->setCustomer($customer);
        } else {
            $order->setCustomer($customerExists);
            $orderCommentText = '';
            // Emails do not match
            if ($customer->getEmail() != $customerExists->getEmail()) {
                $orderCommentText .= 'Email do not match, new email: ' . $customer->getEmail();
            }
            // Phones do not match
            if ($customer->getPhone() != $customerExists->getPhone()) {
                $orderCommentText .='Phone do not match, new phone: ' . $customer->getPhone();
            }
            if ('' != $orderCommentText) {
                $orderComment = new OrderComments();
                $orderComment->setComment($orderCommentText);
                $order->addOrderComment($orderComment);
            }
        }

        $this->setOrderDetails($wcOrder, $order, $timezone, $em, $output);
        return $order;
    }

    public function updateOrderNotes($em, $order, $wcOrderNotes)
    {
        $timezone = new \DateTimeZone('UTC');
        foreach ($wcOrderNotes as $note) {
            $ExistingOrderComment = $em->getRepository('MeVisaERPBundle:OrderComments')->findOneBy(array('wcId' => $note['id']));
            if (!$ExistingOrderComment) {
                $orderComment = new OrderComments();
                $orderComment->setWcId($note['id']);
                if ($note['customer_note']) {
                    $orderComment->setComment($note['note'] . "-- Customer: " . $order->getCustomer()->getName());
                } else {
                    $orderComment->setComment($note['note']);
                }

                $orderComment->setCreatedAt(new \DateTime($note['created_at'], $timezone));
                $order->addOrderComment($orderComment);
            }
        }
    }

    protected function setOrderDetails($wcOrder, $order, $timezone, $em, $output)
    {
        foreach ($wcOrder['line_items'] as $lineItem) {
            $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('wcId' => $lineItem['product_id']));
            if (!$product) {
                $product = new Products();
                $product->setEnabled(true);
                $product->setName($lineItem['name']);
                $product->setWcId($lineItem['product_id']);
                $product->setRequiredDocuments(array());
                $productPrice = new ProductPrices();
                $productPrice->setCost(0);
                $productPrice->setPrice($lineItem['price'] * 100);
                $product->addPricing($productPrice);
                $em->persist($product);
            }

            $productPricing = $product->getPricing();
            $unitPrice = $productPricing[0]->getPrice();
            $orderProduct = new OrderProducts();
            $orderProduct->setProduct($product);
            $orderProduct->setQuantity(($lineItem['total'] * 100) / $unitPrice);
            $orderProduct->setUnitPrice($unitPrice);
            $orderProduct->setTotal($lineItem['total'] * 100);

            $order->addOrderProduct($orderProduct);

            $order->setPeople($lineItem['meta'][0]['value']);

            $departureOffset = 4;
            $arrivalOffset = 5;
            $documentsOffset = 6;
            if ("Дата вылета" == $lineItem['meta'][$departureOffset]['key']) {
                $departure = \DateTime::createFromFormat("d/m/Y", $lineItem['meta'][$departureOffset]['value'], $timezone);
                if (FALSE === $departure) {
                    --$arrivalOffset;
                    --$documentsOffset;
                } else {
                    $order->setDeparture($departure);
                }
            } else {
                --$arrivalOffset;
                --$documentsOffset;
            }

            if ("Дата возврата" == $lineItem['meta'][$arrivalOffset]['key']) {
                $arrival = \DateTime::createFromFormat("d/m/Y", $lineItem['meta'][$arrivalOffset]['value'], $timezone);
                if (FALSE === $arrival) {
                    --$documentsOffset;
                } else {
                    $order->setArrival($arrival);
                }
            } else {
                --$documentsOffset;
            }
            if ("Прикрепите копию паспорта и фото (для всех туристов)" == $lineItem['meta'][$documentsOffset]['key']) {
                $docs = explode(',', $lineItem['meta'][$documentsOffset]['value']);
                foreach ($docs as $doc) {
                    $document = new \MeVisa\ERPBundle\Entity\OrderDocuments();
                    $document->setName($doc);
                    // http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/3975-915-img_9996.jpg
                    $document->setPath('http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/' . $wcOrder['order_number'] . '-' . $lineItem['product_id'] . '-' . $doc);
                    $order->addOrderDocument($document);
                }
            }
        }

        $orderPayment = new OrderPayments();
        // method_id method_title
        $orderPayment->setMethod($wcOrder['payment_details']['method_id']);
        $orderPayment->setAmount($wcOrder['total'] * 100);
        $orderPayment->setCreatedAt(new \DateTime($wcOrder['created_at'], $timezone));
        if ("true" == $wcOrder['payment_details']['paid']) {
            $orderPayment->setState("paid");
        } else {
            $orderPayment->setState("not_paid");
        }
        $order->addOrderPayment($orderPayment);

        if ("" != $wcOrder['note']) {
            $orderComment = new OrderComments();
            $orderComment->setComment($wcOrder['note'] . "-- Customer: " . $order->getCustomer()->getName());
            $orderComment->setCreatedAt(new \DateTime($wcOrder['created_at'], $timezone));
            $order->addOrderComment($orderComment);
        }

        $order->setNumber($wcOrder['order_number']);
        $order->setWcId($wcOrder['order_number']);
        //FIXME: Dynamic channel
        $order->setChannel("MeVisa.ru");

        $order->setAdjustmentTotal($wcOrder['total_discount'] * 100);
        $order->setProductsTotal($wcOrder['subtotal'] * 100);
        $order->setTotal($wcOrder['total'] * 100);
        $order->setState($wcOrder['status']);
        $order->setCreatedAt(new \DateTime($wcOrder['created_at'], $timezone));
    }

}
