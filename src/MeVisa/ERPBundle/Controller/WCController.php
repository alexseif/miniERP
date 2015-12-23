<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\WCLogger;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * WooCommerce Logger controller.
 *
 * @Route("/wc")
 */
class WCController extends Controller
{

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
     */
    public function newAction(Request $request)
    {

        $request = new Request(array(), array(), array(), array(), array(), array(), '{"order":{"id":3853,"order_number":3853,"created_at":"2015-12-21T12:01:28Z","updated_at":"2015-12-21T12:01:28Z","completed_at":"2015-12-21T12:01:28Z","status":"pending","currency":"RUB","total":"29200.00","subtotal":"29200.00","total_line_items_quantity":1,"total_tax":"0.00","total_shipping":"0.00","cart_tax":"0.00","shipping_tax":"0.00","total_discount":"0.00","shipping_methods":"","payment_details":{"method_id":"payu","method_title":"PayU","paid":false},"billing_address":{"first_name":"\u041a\u0430\u0440\u0435\u043d","last_name":"\u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":"","email":"karen_m@rambler.ru","phone":"89296694700"},"shipping_address":{"first_name":"\u041a\u0430\u0440\u0435\u043d","last_name":"\u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":""},"note":"","customer_ip":"46.39.50.250","customer_user_agent":"Mozilla\/5.0 (Windows NT 6.1; WOW64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/46.0.2490.71 Safari\/537.36","customer_id":0,"view_order_url":"http:\/\/www.mevisa.ru\/my-account\/view-order\/3853","line_items":[{"id":1068,"subtotal":"29200.00","subtotal_tax":"0.00","total":"29200.00","total_tax":"0.00","price":"29200.00","quantity":1,"tax_class":null,"name":"\u0412\u0418\u0417\u0410 \u0412 \u041e\u0410\u042d \u043d\u0430 14 \u0434\u043d\u0435\u0439","product_id":3263,"sku":"","meta":[{"key":"\u041a\u043e\u043b-\u0432\u043e \u0447\u0435\u043b\u043e\u0432\u0435\u043a","label":"\u041a\u043e\u043b \u0432\u043e \u0447\u0435\u043b\u043e\u0432\u0435\u043a","value":"4"},{"key":"\u0418\u043c\u044f \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","label":"\u0418\u043c\u044f \u0437\u0430\u043a\u0430\u0437\u0447\u0438\u043a\u0430","value":"\u041a\u0430\u0440\u0435\u043d \u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d"},{"key":"E-mail","label":"E Mail","value":"karen_m@rambler.ru"},{"key":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d","label":"\u0422\u0435\u043b\u0435\u0444\u043e\u043d","value":"+7(929)6694700"},{"key":"\u0414\u0430\u0442\u0430 \u0432\u044b\u043b\u0435\u0442\u0430","label":"\u0414\u0430\u0442\u0430 \u0432\u044b\u043b\u0435\u0442\u0430","value":"28\/12\/2015"},{"key":"\u0414\u0430\u0442\u0430 \u0432\u043e\u0437\u0432\u0440\u0430\u0442\u0430","label":"\u0414\u0430\u0442\u0430 \u0432\u043e\u0437\u0432\u0440\u0430\u0442\u0430","value":"07\/01\/2016"},{"key":"\u041f\u0440\u0438\u043a\u0440\u0435\u043f\u0438\u0442\u0435 \u043a\u043e\u043f\u0438\u044e \u043f\u0430\u0441\u043f\u043e\u0440\u0442\u0430 \u0438 \u0444\u043e\u0442\u043e (\u0434\u043b\u044f \u0432\u0441\u0435\u0445 \u0442\u0443\u0440\u0438\u0441\u0442\u043e\u0432)","label":"\u041f\u0440\u0438\u043a\u0440\u0435\u043f\u0438\u0442\u0435 \u043a\u043e\u043f\u0438\u044e \u043f\u0430\u0441\u043f\u043e\u0440\u0442\u0430 \u0438 \u0444\u043e\u0442\u043e (\u0434\u043b\u044f \u0432\u0441\u0435\u0445 \u0442\u0443\u0440\u0438\u0441\u0442\u043e\u0432)","value":"\u0411\u0440\u043e\u043d\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435-\u043f\u0443\u0442\u0435\u0448\u0435\u0441\u0442\u0432\u0438\u044f-28-\u0414\u0435\u043a\u0430\u0431\u0440\u044c-\u043d\u0430-gracheva.pdf,\u0414\u0435\u0442\u0438_\u0437\u0430\u0433\u0440\u0430\u043d.pdf,\u041a\u0432\u0438\u0442\u0430\u043d\u0446\u0438\u044f-\u044d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0433\u043e-\u0431\u0438\u043b\u0435\u0442\u0430-28-\u0414\u0435\u043a\u0430\u0431\u0440\u044c-\u043a\u043b\u0438\u0435\u043d\u0442_-artem-mamakin.pdf,\u041a\u0432\u0438\u0442\u0430\u043d\u0446\u0438\u044f-\u044d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0433\u043e-\u0431\u0438\u043b\u0435\u0442\u0430-28-\u0414\u0435\u043a\u0430\u0431\u0440\u044c-\u043a\u043b\u0438\u0435\u043d\u0442_-karen-matevosyan.pdf,\u041a\u0432\u0438\u0442\u0430\u043d\u0446\u0438\u044f-\u044d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0433\u043e-\u0431\u0438\u043b\u0435\u0442\u0430-28-\u0414\u0435\u043a\u0430\u0431\u0440\u044c-\u043a\u043b\u0438\u0435\u043d\u0442_-marina-gracheva.pdf,\u041a\u0432\u0438\u0442\u0430\u043d\u0446\u0438\u044f-\u044d\u043b\u0435\u043a\u0442\u0440\u043e\u043d\u043d\u043e\u0433\u043e-\u0431\u0438\u043b\u0435\u0442\u0430-28-\u0414\u0435\u043a\u0430\u0431\u0440\u044c-\u043a\u043b\u0438\u0435\u043d\u0442_-vasily-mamakin.pdf,\u041f\u0430\u0441\u043f\u043e\u0440\u0442-\u041a\u0430\u0440\u0435\u043d-\u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d.jpeg,\u041f\u0430\u0441\u043f\u043e\u0440\u0442-\u041c\u0430\u0440\u0438\u043d\u0430-\u0413\u0440\u0430\u0447\u0435\u0432\u0430.pdf,\u0441\u0432\u0438\u0434_\u0440\u043e\u0436\u0434\u0435\u043d-\u0410\u0440\u0442\u0435\u043c-\u041c\u0430\u043c\u0430\u043a\u0438\u043d.pdf,\u0441\u0432\u0438\u0434_\u0440\u043e\u0436\u0434\u0435\u043d-\u0412\u0430\u0441\u0438\u043b\u0438\u0439-\u041c\u0430\u043c\u0430\u043a\u0438\u043d.pdf,\u0424\u043e\u0442\u043e-\u0410\u0440\u0442\u0435\u043c-\u041c\u0430\u043c\u0430\u043a\u0438\u043d.jpg,\u0424\u043e\u0442\u043e-\u0412\u0430\u0441\u0438\u043b\u0438\u0439-\u041c\u0430\u043c\u0430\u043a\u0438\u043d.jpg,\u0424\u043e\u0442\u043e-\u041a\u0430\u0440\u0435\u043d-\u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d.jpg,\u0424\u043e\u0442\u043e-\u041c\u0430\u0440\u0438\u043d\u0430-\u0413\u0440\u0430\u0447\u0435\u0432\u0430.jpg"}]}],"shipping_lines":[],"tax_lines":[],"fee_lines":[],"coupon_lines":[],"customer":{"id":0,"email":"karen_m@rambler.ru","first_name":"\u041a\u0430\u0440\u0435\u043d","last_name":"\u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d","billing_address":{"first_name":"\u041a\u0430\u0440\u0435\u043d","last_name":"\u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":"","email":"karen_m@rambler.ru","phone":"89296694700"},"shipping_address":{"first_name":"\u041a\u0430\u0440\u0435\u043d","last_name":"\u041c\u0430\u0442\u0435\u0432\u043e\u0441\u044f\u043d","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":""}}}}');
        $request->setMethod('POST');
        $request->headers->set('content-type', 'application/json');
        $request->headers->set('x-wc-webhook-topic', 'order.created');
        $request->headers->set('x-wc-webhook-resource', 'order');
        $request->headers->set('x-wc-webhook-event', 'created');
        $request->headers->set('x-wc-webhook-signature', '3ioeYkEkBdemp7zDF+ZX2HqhFcy6EdSgwTr87DaJoBU=');
        $request->headers->set('x-wc-webhook-id', '3840');
        $request->headers->set('x-wc-webhook-delivery-id', '2535');

        $secret = 'kfxLneHxN7';
        $content = trim($request->getContent());
        $header = $request->headers->all();

        $signature = $this->generateSignature($secret, $request->getContent());

        if ($request->headers->get('x-wc-webhook-signature') != $signature) {
            throw new HttpException(422, "Signature mismatch");
        }

        $wcLogger = new WCLogger();

        $wcLogger->setHeader(json_encode($header));
        $wcLogger->setContent($request->getContent());

        $em = $this->getDoctrine()->getManager();
        $em->persist($wcLogger);
        $em->flush();

        if (
                ('order' != $request->headers->get('x-wc-webhook-resource')) ||
                ('created' != $request->headers->get('x-wc-webhook-event'))
        ) {
            throw new HttpException(422, "Unacceptable reource or event");
        }

        $wcOrder = json_decode($content, true);
        $wcOrder = $wcOrder['order'];

        $order = $em->getRepository('MeVisaERPBundle:Orders')->findOneBy(array('wcId' => $wcOrder['order_number']));
        if ($order) {
            throw new HttpException(409, "Order exists");
        }
        $order = new \MeVisa\ERPBundle\Entity\Orders();

        $customer = new \MeVisa\CRMBundle\Entity\Customers();
        $customer->setName($wcOrder['billing_address']['first_name'] . ' ' . $wcOrder['billing_address']['last_name']);
        $customer->setEmail($wcOrder['billing_address']['email']);
        $customer->setPhone($wcOrder['billing_address']['phone']);

        $customerExists = $em->getRepository('MeVisaCRMBundle:Customers')->findOneBy(array('name' => $customer->getName()));
var_dump($customerExists);die();
        //FIXME: not Working
        if (!$customerExists) {
            $em->persist($customer);
        } else {
            $orderCommentText = '';
            //Emails do not match
            if ($customer->getEmail() != $customerExists->getEmail()) {
                $orderCommentText .= 'Email do not match, new email: ' . $customer->getEmail();
            }

            //Phones do not match
            if ($customer->getPhone() != $customerExists->getPhone()) {
                $orderCommentText .='Phone do not match, new phone: ' . $customer->getPhone();
            }
            if ('' != $orderCommentText) {
                $orderComment = new \MeVisa\ERPBundle\Entity\OrderComments();
                $orderComment->setComment($orderCommentText);
                $order->addOrderComment($orderComment);
            }
        }
        $order->setCustomer($customer);

        //TODO: Align Product
        foreach ($wcOrder['line_items'] as $lineItem) {
            $product = $em->getRepository('MeVisaERPBundle:Products')->findOneBy(array('wcId' => $lineItem['product_id']));
            if (!$product) {
                $product = new \MeVisa\ERPBundle\Entity\Products();
                $product->setEnabled(true);
                $product->setName($lineItem['name']);
                $product->setWcId($lineItem['product_id']);
                $product->setRequiredDocuments(array());
                $productPrice = new \MeVisa\ERPBundle\Entity\ProductPrices();
                $productPrice->setCost(0);
                $productPrice->setPrice($lineItem['price']);
                $product->addPricing($productPrice);
                $em->persist($product);
            }
            $orderProduct = new \MeVisa\ERPBundle\Entity\OrderProducts();
            $orderProduct->setProduct($product);
            $orderProduct->setQuantity($lineItem['quantity']);
            $orderProduct->setUnitPrice($lineItem['price']);
            $orderProduct->setTotal($lineItem['total']);

            $order->addOrderProduct($orderProduct);

            //TODO: Fix this
            //$lineItem['meta'] holds the companions
        }

        $orderPayment = new \MeVisa\ERPBundle\Entity\OrderPayments();
        // method_id method_title
        $orderPayment->setMethod($wcOrder['payment_details']['method_id']);
        if ($wcOrder['payment_details']['paid']) {
            $orderPayment->setState("not_paid");
        } else {
            $orderPayment->setState("paid");
        }

        if ("" != $wcOrder['note']) {
            $orderComment = new \MeVisa\ERPBundle\Entity\OrderComments();
            $orderComment->setComment($wcOrder['note']);
            $orderComment->setAuthor("Customer: " . $customer->getName());
            $orderComment->setCreatedAt(new \DateTime($wcOrder['created_at']));
            $order->addOrderComment($orderComment);
        }

        $order->setNumber($wcOrder['order_number']);
        $order->setWcId($wcOrder['order_number']);
        $order->setChannel("MeVisa.ru");

        $order->setAdjustmentTotal($wcOrder['total_discount']);
        $order->setProductsTotal($wcOrder['subtotal']);
        $order->setTotal($wcOrder['total']);
        $order->setState($wcOrder['status']);
        $order->setCreatedAt(new \DateTime($wcOrder['created_at']));

        $em->persist($order);
        $em->flush();

        return new Response();
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

}
