<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\WCLogger;
use MeVisa\ERPBundle\Entity\Orders;
use MeVisa\ERPBundle\Entity\OrderProducts;
use MeVisa\ERPBundle\Entity\OrderPayments;
use MeVisa\ERPBundle\Entity\OrderComments;
use MeVisa\ERPBundle\Entity\Products;
use MeVisa\ERPBundle\Entity\ProductPrices;
use MeVisa\CRMBundle\Entity\Customers;
use MeVisa\ERPBundle\WCAPI\RESTAPI;

/**
 * WooCommerce Logger controller.
 *
 * @Route("/wc")
 */
class WCController extends Controller
{
    //TODO: add method of integration (webhook/api)
    //TODO: save notes

    /**
     * Lists all WCLogger entities.
     *
     * @Route("/", name="wc")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:WCLogger')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Displays a form to create a new WC entity.
     *
     * @Route("/new", name="wc_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
//        $secret = 'kfxLneHxN7';
//        $content = trim($request->getContent());
//        $header = $request->headers->all();
//
//        $wcLogger = new WCLogger();
//
//        $wcLogger->setHeader(json_encode($header));
//        $wcLogger->setContent($request->getContent());
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($wcLogger);
//        $em->flush();
//
//        $signature = $this->generateSignature($secret, $request->getContent());
//        if ($request->headers->get('x-wc-webhook-signature') != $signature) {
//            $wcLogger->setResult('Signature mismatch');
//            $em->persist($wcLogger);
//            $em->flush();
//            return new Response('Signature mismatch');
//        }
//
//        if (
//                ('order' != $request->headers->get('x-wc-webhook-resource')) ||
//                ('created' != $request->headers->get('x-wc-webhook-event'))
//        ) {
//            $wcLogger->setResult('Unacceptable resource or event');
//            $em->persist($wcLogger);
//            $em->flush();
//            return new Response('Unacceptable resource or event');
//        }
//
//        $wcOrder = json_decode($content, true);
//        $wcOrder = $wcOrder['order'];
//
//        $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('wcId' => $wcOrder['order_number']));
//        if ($order) {
//            $wcLogger->setResult('Order exists');
//            $em->persist($wcLogger);
//            $em->flush();
//            return new Response('Order exists');
//        }
//
//        $this->newOrder($em, $wcOrder);
//
//
//        $wcLogger->setResult('OK');
//        $em->persist($wcLogger);
//
//        $em->flush();

        $this->apiAction();

        return new Response();
    }

    public function apiAction()
    {
        $client = new RESTAPI();
        //TODO: make a function that tests the webhook is active periodically, if not create a new one
        //TODO: make a function that keeps checking on orders
        $em = $this->getDoctrine()->getManager();
        $wcOrders = $client->getCompletedOrders();
        foreach ($wcOrders['orders'] as $wcOrder) {
            $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('wcId' => $wcOrder['order_number']));
            if ($order) {
                if (\is_null($order->getUpdatedAt())) {
                    $order = $this->updateOrder($em, $wcOrder, $order);
                }
            } else {
                $order = $this->newOrder($em, $wcOrder);
            }
            $wcOrderNotes = $client->getOrderNotes($wcOrder['order_number']);
            $this->updateOrderNotes($em, $order, $wcOrderNotes['order_notes']);

            $em->persist($order);
        }
        $em->flush();

// TODO: parse links
//        $links = explode(',', $order->response->headers['Link']);
//        foreach ($links as $link) {
//            $linkPart = explode(';', $link);
//            if (count($linkPart) < 2)
//                continue;
//            $links[] = $linkPart;
//        }
        return array(
            'wcOrders' => $wcOrders,
        );
    }

    /**
     * Finds and displays a WC entity.
     *
     * @Route("/{id}", name="wc_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:WCLogger')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WCLogger entity.');
        }
        $entity->setHeader(json_decode($entity->getHeader(), true));
        $entity->setContent(json_decode($entity->getContent(), true));

        return array(
            'entity' => $entity,
        );
    }

    function generateSignature($webhook_key, $params)
    {
        return base64_encode(hash_hmac('sha256', $params, $webhook_key, true));
    }

    public function newOrder($em, $wcOrder)
    {
        $timezone = new \DateTimeZone('UTC');

        $order = new Orders();

        $customer = new Customers();
        $customer->setName($wcOrder['billing_address']['first_name'] . ' ' . $wcOrder['billing_address']['last_name']);
        $customer->setEmail($wcOrder['billing_address']['email']);
        $customer->setPhone($wcOrder['billing_address']['phone']);

        $customerExists = $em->getRepository('MeVisaCRMBundle:Customers')->findOneBy(array('name' => $customer->getName()));

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

        $this->setOrderDetails($wcOrder, $order, $timezone, $em);
        return $order;
    }

    public function updateOrder($em, $wcOrder, $order)
    {
        $timezone = new \DateTimeZone('UTC');
        $order->startOrderStateEnginge();

        $orderDocuments = $order->getOrderDocuments();
        foreach ($orderDocuments as $orderDocument) {
            $order->removeOrderDocument($orderDocument);
            $em->remove($orderDocument);
        }

        $orderProducts = $order->getOrderProducts();
        foreach ($orderProducts as $orderProduct) {
            $order->removeOrderProduct($orderProduct);
            $em->remove($orderProduct);
        }
        $orderPayments = $order->getOrderPayments();
        foreach ($orderPayments as $orderPayment) {
            $order->removeOrderPayment($orderPayment);
            $em->remove($orderPayment);
        }

        $order->getCustomer()->setName($wcOrder['billing_address']['first_name'] . ' ' . $wcOrder['billing_address']['last_name']);
        $order->getCustomer()->setEmail($wcOrder['billing_address']['email']);
        $order->getCustomer()->setPhone($wcOrder['billing_address']['phone']);

        $this->setOrderDetails($wcOrder, $order, $timezone, $em);
        return $order;
    }

    public function updateOrderNotes($em, $order, $wcOrderNotes)
    {
        $timezone = new \DateTimeZone('UTC');
        foreach ($wcOrderNotes as $note) {
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

    protected function setOrderDetails($wcOrder, $order, $timezone, $em)
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

            $orderProduct = new OrderProducts();
            $orderProduct->setProduct($product);
            $orderProduct->setQuantity($lineItem['quantity']);
            $orderProduct->setUnitPrice($lineItem['price'] * 100);
            $orderProduct->setTotal($lineItem['total'] * 100);

            $order->addOrderProduct($orderProduct);

            $order->setPeople($lineItem['meta'][0]['value']);

            $order->setDeparture(\DateTime::createFromFormat("d/m/Y", $lineItem['meta'][4]['value'], $timezone));
            $order->setArrival(\DateTime::createFromFormat("d/m/Y", $lineItem['meta'][5]['value']), $timezone);

            //FIXME: check available docs first
            $docs = explode(',', $lineItem['meta'][6]['value']);
            foreach ($docs as $doc) {
                $document = new \MeVisa\ERPBundle\Entity\OrderDocuments();
                $document->setName($doc);
                // http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/3975-915-img_9996.jpg
                $document->setPath('http://www.mevisa.ru/wp-content/uploads/product_files/confirmed/' . $wcOrder['order_number'] . '-' . $lineItem['product_id'] . '-' . $doc);
                $order->addOrderDocument($document);
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
