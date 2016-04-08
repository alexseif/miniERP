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
use WC_API_Client_HTTP_Exception;

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
        $wcOrdersSecondPage = $wcOrders = null;
        try {
            $client = new RESTAPI();
            $wcOrders = $client->getCompletedOrders();
            $wcOrdersSecondPage = $client->getCompletedOrdersSecondPage();
        } catch (WC_API_Client_HTTP_Exception $exc) {
            //TODO: do something
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        if ($wcOrders) {
            foreach ($wcOrders['orders'] as $wcOrder) {
                if (true == $wcOrder['payment_details']['paid']) {
                    $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('wcId' => $wcOrder['order_number']));
                    if (!$order) {
                        $order = $this->newOrder($em, $wcOrder);
                    }
                    $em->persist($order);
                }
//            $wcOrderNotes = $client->getOrderNotes($wcOrder['order_number']);
//            $this->updateOrderNotes($em, $order, $wcOrderNotes['order_notes']);
            }
        }

        if ($wcOrdersSecondPage) {
            foreach ($wcOrdersSecondPage['orders'] as $wcOrder) {
                if (true == $wcOrder['payment_details']['paid']) {
                    $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('wcId' => $wcOrder['order_number']));
                    if (!$order) {
                        $order = $this->newOrder($em, $wcOrder);
                    }
                    $em->persist($order);
                }
            }
        }
        $em->flush();
    }

    public function newOrder($em, $wcOrder)
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
            if ($customer->getName() != $customerExists->getName()) {
                //TODO: do something
                //$customerExists->setName($customer->getName());
            }
            if ($customer->getEmail() != $customerExists->getEmail()) {
                //TODO: do something
            }
            // Phones do not match
            if ($customer->getPhone() != $customerExists->getPhone()) {
                //TODO: do something
                $customerExists->setPhone($customer->getPhone());
            }
            if ('' != $orderCommentText) {
                $orderComment = new OrderComments();
                $orderComment->setComment($orderCommentText);
                $order->addOrderComment($orderComment);
            }
        }

        $this->setOrderDetails($wcOrder, $order, $timezone, $em);
        return $order;
    }

    protected function setOrderDetails($wcOrder, $order, $timezone, $em)
    {
        $order->setWcId($wcOrder['order_number']);
        $order->setNumber($wcOrder['order_number']);
        $order->setCreatedAt(new \DateTime($wcOrder['created_at'], $timezone));
        switch ($wcOrder['status']) {
            case "cancelled":
            case "failed":
                $state = "cancelled";
                break;
            case "refunded":
                $state = "refunded";
                break;
            case "pending":
            case "processing":
            case "on-hold":
            case "completed":
            default:
                $state = "backoffice";
                break;
        }

        $order->setState($state);
        $order->setTotal($wcOrder['total'] * 100);
        $order->setProductsTotal($wcOrder['subtotal'] * 100);
        $order->setPeople(0);
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

            $productPrice = $product->getPricing()->last();
            $orderProduct = new OrderProducts();
            $orderProduct->setProduct($product);
            $orderProduct->setQuantity($lineItem['quantity']);
            $orderProduct->setUnitPrice($lineItem['price'] * 100);
            $orderProduct->setUnitCost($productPrice->getCost());
            $orderProduct->setVendor($product->getVendor());
            $orderProduct->setTotal($lineItem['total'] * 100);

            $order->addOrderProduct($orderProduct);

            foreach ($lineItem['meta'] as $key => $meta) {
                switch ($meta['key']) {
                    case'Кол-во человек':
                        $order->setPeople($order->getPeople() + $meta['value']);
                        break;
                    case'Дата вылета':
                        $arrival = \DateTime::createFromFormat("d/m/Y", $meta['value'], $timezone);
                        $order->setArrival($arrival);
                        break;
                    case'Дата возврата':
                        $departure = \DateTime::createFromFormat("d/m/Y", $meta['value'], $timezone);
                        $order->setDeparture($departure);
                        break;
                    case'Прикрепите копию паспорта и фото (для всех туристов)':
                        $docs = explode(',', $meta['value']);
                        foreach ($docs as $doc) {
                            $document = new \MeVisa\ERPBundle\Entity\OrderDocuments();
                            $document->setName($doc);
                            // http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/3975-915-img_9996.jpg
                            $document->setPath('http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/' . $wcOrder['order_number'] . '-' . $lineItem['product_id'] . '-' . $doc);
                            $order->addOrderDocument($document);
                        }
                        break;
                    default :
                        break;
                }
            }
        }

        $order->setAdjustmentTotal($wcOrder['total_discount'] * 100);
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

        //FIXME: Dynamic channel
        $order->setChannel("MeVisa.ru");


        $order->setTicketRequired(false);
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

}
