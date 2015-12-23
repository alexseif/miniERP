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

        //FIXME: not Working
        if (!$customerExists) {
            $em->persist($customer);
            $order->setCustomer($customer);
        } else {
            $order->setCustomer($customerExists);
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
