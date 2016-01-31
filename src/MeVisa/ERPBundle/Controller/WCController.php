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
                $order = $this->updateOrder($em, $wcOrder, $order);
            } else {
                $order = $this->newOrder($em, $wcOrder);
            }
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

    function applyTest()
    {
        if ("dev" == $this->container->get('kernel')->getEnvironment()) {
            $header = json_decode('{"host":"visallc.nichost.ru","x-forwarded-for":"37.140.192.17, 37.140.192.17","connection":"close","content-length":"3416","user-agent":"WooCommerce\/2.3.8 Hookshot (WordPress\/4.4.1)","accept":"*\/*","content-type":"application\/json","x-wc-webhook-topic":"order.created","x-wc-webhook-resource":"order","x-wc-webhook-event":"created","x-wc-webhook-signature":"PP+Wy52atHuaKIeW0+B45KgDpU6vxDYwHNrGA\/I\/EDE=","x-wc-webhook-id":"3873","x-wc-webhook-delivery-id":"2808","accept-encoding":"deflate;q=1.0, compress;q=0.5","x-php-ob-level":0}', true);
            $content = '{"order":{"id":3911,"order_number":3911,"created_at":"2016-01-10T21:57:21Z","updated_at":"2016-01-10T21:57:21Z","completed_at":"2016-01-10T21:57:21Z","status":"pending","currency":"RUB","total":"7700.00","subtotal":"7700.00","total_line_items_quantity":1,"total_tax":"0.00","total_shipping":"0.00","cart_tax":"0.00","shipping_tax":"0.00","total_discount":"0.00","shipping_methods":"","payment_details":{"method_id":"payu","method_title":"PayU","paid":false},"billing_address":{"first_name":"Margarita","last_name":"Balinyan","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":"","email":"nananeskaju@gmail.com","phone":"+79859912034"},"shipping_address":{"first_name":"Margarita","last_name":"Balinyan","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":""},"note":"","customer_ip":"203.125.211.70","customer_user_agent":"Mozilla\/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/47.0.2526.106 Safari\/537.36","customer_id":0,"view_order_url":"http:\/\/www.mevisa.ru\/my-account\/view-order\/3911","line_items":[{"id":1130,"subtotal":"7700.00","subtotal_tax":"0.00","total":"7700.00","total_tax":"0.00","price":"7700.00","quantity":1,"tax_class":null,"name":"\u0412\u0418\u0417\u0410 \u0412 \u041e\u0410\u042d","product_id":915,"sku":"","meta":[{"key":"\u041a\u043e\u043b-\u0432\u043e \u0447\u0435\u043b\u043e\u0432\u0435\u043a","label":"\u041a\u043e\u043b \u0432\u043e \u0447\u0435\u043b\u043e\u0432\u0435\u043a","value":"1"},{"key":"\u0418\u043c\u044f \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","label":"\u0418\u043c\u044f \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","value":"Margarita Balinyan"},{"key":"E-mail","label":"E Mail","value":"nananeskaju@gmail.com"},{"key":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d","label":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d","value":"+79859912034"},{"key":"\u0414\u0430\u0442\u0430 \u0432\u044b\u043b\u0435\u0442\u0430","label":"\u0414\u0430\u0442\u0430 \u0432\u044b\u043b\u0435\u0442\u0430","value":"22\/01\/2016"},{"key":"\u0414\u0430\u0442\u0430 \u0432\u043e\u0437\u0432\u0440\u0430\u0442\u0430","label":"\u0414\u0430\u0442\u0430 \u0432\u043e\u0437\u0432\u0440\u0430\u0442\u0430","value":"25\/01\/2016"},{"key":"\u041f\u0440\u0438\u043a\u0440\u0435\u043f\u0438\u0442\u0435 \u043a\u043e\u043f\u0438\u044e \u043f\u0430\u0441\u043f\u043e\u0440\u0442\u0430 \u0438 \u0444\u043e\u0442\u043e (\u0434\u043b\u044f \u0432\u0441\u0435\u0445 \u0442\u0443\u0440\u0438\u0441\u0442\u043e\u0432)","label":"\u041f\u0440\u0438\u043a\u0440\u0435\u043f\u0438\u0442\u0435 \u043a\u043e\u043f\u0438\u044e \u043f\u0430\u0441\u043f\u043e\u0440\u0442\u0430 \u0438 \u0444\u043e\u0442\u043e (\u0434\u043b\u044f \u0432\u0441\u0435\u0445 \u0442\u0443\u0440\u0438\u0441\u0442\u043e\u0432)","value":"puma2.pdf,zagran.pdf"}]}],"shipping_lines":[],"tax_lines":[],"fee_lines":[],"coupon_lines":[],"customer":{"id":0,"email":"nananeskaju@gmail.com","first_name":"Margarita","last_name":"Balinyan","billing_address":{"first_name":"Margarita","last_name":"Balinyan","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":"","email":"nananeskaju@gmail.com","phone":"+79859912034"},"shipping_address":{"first_name":"Margarita","last_name":"Balinyan","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":""}}}}';
            $request = new Request(array(), array(), array(), array(), array(), array(), $content);

            foreach ($header as $key => $value) {
                $request->headers->set($key, $value, true);
            }
            return $request;
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
