<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\WCLogger;

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
        // TODO: Verify request
        // Secret kfxLneHxN7
        $validRequest = true;

        // $request = Request::createFromGlobals();
        $entity = new WCLogger();

        $entity->setHeader(json_encode($request->headers->all()));
        $entity->setContent(json_encode($request->getContent()));

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        //TODO: Align Product
        //TODO: Align Customer
        //TODO: Align Order
        //TODO: Create order

        /**
         * Header
          Метод: POST

          Длительность: http://visallc.nichost.ru/wc

          Колонтитулы:

          user-agent: WooCommerce/2.3.8 Hookshot (WordPress/4.4)
          content-type: application/json
          x-wc-webhook-topic: order.created
          x-wc-webhook-resource: order
          x-wc-webhook-event: created
          x-wc-webhook-signature: 2C2n379EjjwYBxYCQv1P3ICe/zz7odukZCgWEsfPgfw=
          x-wc-webhook-id: 3840
          x-wc-webhook-delivery-id: 2497

         */
        /**
         * Content
          {"order":{"id":3843,"order_number":3843,"created_at":"2015-12-19T19:28:46Z","updated_at":"2015-12-19T19:28:46Z","completed_at":"2015-12-19T19:28:46Z","status":"pending","currency":"RUB","total":"7300.00","subtotal":"7300.00","total_line_items_quantity":1,"total_tax":"0.00","total_shipping":"0.00","cart_tax":"0.00","shipping_tax":"0.00","total_discount":"0.00","shipping_methods":"","payment_details":{"method_id":"payu","method_title":"PayU","paid":false},"billing_address":{"first_name":"u041cu0430u0440u043a","last_name":"u0418u0433u043du0430u0442u044cu0435u0432","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":"","email":"ridos@yandex.ru","phone":"+79882481548"},"shipping_address":{"first_name":"u041cu0430u0440u043a","last_name":"u0418u0433u043du0430u0442u044cu0435u0432","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":""},"note":"","customer_ip":"31.181.188.91","customer_user_agent":"Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko","customer_id":0,"view_order_url":"http://www.mevisa.ru/my-account/view-order/3843","line_items":[{"id":1058,"subtotal":"7300.00","subtotal_tax":"0.00","total":"7300.00","total_tax":"0.00","price":"7300.00","quantity":1,"tax_class":null,"name":"u0412u0418u0417u0410 u0412 u041eu0410u042d u043du0430 14 u0434u043du0435u0439","product_id":3263,"sku":"","meta":[{"key":"u041au043eu043b-u0432u043e u0447u0435u043bu043eu0432u0435u043a","label":"u041au043eu043b u0432u043e u0447u0435u043bu043eu0432u0435u043a","value":"1"},{"key":"u0418u043cu044f u0437u0430u043au0430u0437u0447u0438u043au0430","label":"u0418u043cu044f u0437u0430u043au0430u0437u0447u0438u043au0430","value":"u041cu0430u0440u043a"},{"key":"E-mail","label":"E Mail","value":"ridos@yandex.ru"},{"key":"u0422u0435u043bu0435u0444u043eu043d","label":"u0422u0435u043bu0435u0444u043eu043d","value":"+79882481548"},{"key":"u0414u0430u0442u0430 u0432u044bu043bu0435u0442u0430","label":"u0414u0430u0442u0430 u0432u044bu043bu0435u0442u0430","value":"05/01/2016"},{"key":"u0414u0430u0442u0430 u0432u043eu0437u0432u0440u0430u0442u0430","label":"u0414u0430u0442u0430 u0432u043eu0437u0432u0440u0430u0442u0430","value":"12/01/2016"},{"key":"u041fu0440u0438u043au0440u0435u043fu0438u0442u0435 u043au043eu043fu0438u044e u043fu0430u0441u043fu043eu0440u0442u0430 u0438 u0444u043eu0442u043e (u0434u043bu044f u0432u0441u0435u0445 u0442u0443u0440u0438u0441u0442u043eu0432)","label":"u041fu0440u0438u043au0440u0435u043fu0438u0442u0435 u043au043eu043fu0438u044e u043fu0430u0441u043fu043eu0440u0442u0430 u0438 u0444u043eu0442u043e (u0434u043bu044f u0432u0441u0435u0445 u0442u0443u0440u0438u0441u0442u043eu0432)","value":"asanova-lenura-05jan-krr_1.pdf,ignateva-diana-05jan-krr_1.pdf,ignateva-eva-05jan-krr_1.pdf,ignateva-milena-05jan-krr_1.pdf,ignateva-simona-05jan-krr_1.pdf,ignatyev-mark-05jan-krr_1.pdf,ignatyeva-tatiana-05jan-krr_1.pdf,u041bu0435u043du0443u0440u0430-1.jpg,u0410u0441u0430u043du043eu0432u0430-u041bu0435u043du0443u0440u0430-u0428u0435u0432u043au0435u0442u043eu0432u043du0430-u0437u0430u0433u0440u0430u043d-1.jpeg"}]}],"shipping_lines":[],"tax_lines":[],"fee_lines":[],"coupon_lines":[],"customer":{"id":0,"email":"ridos@yandex.ru","first_name":"u041cu0430u0440u043a","last_name":"u0418u0433u043du0430u0442u044cu0435u0432","billing_address":{"first_name":"u041cu0430u0440u043a","last_name":"u0418u0433u043du0430u0442u044cu0435u0432","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":"","email":"ridos@yandex.ru","phone":"+79882481548"},"shipping_address":{"first_name":"u041cu0430u0440u043a","last_name":"u0418u0433u043du0430u0442u044cu0435u0432","company":"","address_1":"","address_2":"","city":"","state":"","postcode":"","country":""}}}}
         */
        $data = array(
            "order" => array(
                "id" => 3843,
                "order_number" => 3843,
                "created_at" => "2015-12-19T19=>28=>46Z",
                "updated_at" => "2015-12-19T19=>28=>46Z",
                "completed_at" => "2015-12-19T19=>28=>46Z",
                "status" => "pending",
                "currency" => "RUB",
                "total" => "7300.00",
                "subtotal" => "7300.00",
                "total_line_items_quantity" => 1,
                "total_tax" => "0.00",
                "total_shipping" => "0.00",
                "cart_tax" => "0.00",
                "shipping_tax" => "0.00",
                "total_discount" => "0.00",
                "shipping_methods" => "",
                "payment_details" => array(
                    "method_id" => "payu",
                    "method_title" => "PayU",
                    "paid" => false
                ),
                "billing_address" => array(
                    "first_name" => "u041cu0430u0440u043a",
                    "last_name" => "u0418u0433u043du0430u0442u044cu0435u0432",
                    "company" => "",
                    "address_1" => "",
                    "address_2" => "",
                    "city" => "",
                    "state" => "",
                    "postcode" => "",
                    "country" => "",
                    "email" => "ridos@yandex.ru",
                    "phone" => "+79882481548"
                ),
                "shipping_address" => array(
                    "first_name" => "u041cu0430u0440u043a",
                    "last_name" => "u0418u0433u043du0430u0442u044cu0435u0432",
                    "company" => "",
                    "address_1" => "",
                    "address_2" => "",
                    "city" => "",
                    "state" => "",
                    "postcode" => "",
                    "country" => ""
                ),
                "note" => "",
                "customer_ip" => "31.181.188.91",
                "customer_user_agent" => "Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv=>11.0) like Gecko",
                "customer_id" => 0,
                "view_order_url" => "http=>//www.mevisa.ru/my-account/view-order/3843",
                "line_items" => array(
                    array(
                        "id" => 1058,
                        "subtotal" => "7300.00",
                        "subtotal_tax" => "0.00",
                        "total" => "7300.00",
                        "total_tax" => "0.00",
                        "price" => "7300.00",
                        "quantity" => 1,
                        "tax_class" => null,
                        "name" => "u0412u0418u0417u0410 u0412 u041eu0410u042d u043du0430 14 u0434u043du0435u0439",
                        "product_id" => 3263,
                        "sku" => "",
                        "meta" => array(
                            array(
                                "key" => "u041au043eu043b-u0432u043e u0447u0435u043bu043eu0432u0435u043a",
                                "label" => "u041au043eu043b u0432u043e u0447u0435u043bu043eu0432u0435u043a",
                                "value" => "1"
                            ), array(
                                "key" => "u0418u043cu044f u0437u0430u043au0430u0437u0447u0438u043au0430",
                                "label" => "u0418u043cu044f u0437u0430u043au0430u0437u0447u0438u043au0430",
                                "value" => "u041cu0430u0440u043a"
                            ), array(
                                "key" => "E-mail",
                                "label" => "E Mail",
                                "value" => "ridos@yandex.ru"
                            ), array(
                                "key" => "u0422u0435u043bu0435u0444u043eu043d",
                                "label" => "u0422u0435u043bu0435u0444u043eu043d",
                                "value" => "+79882481548"
                            ), array(
                                "key" => "u0414u0430u0442u0430 u0432u044bu043bu0435u0442u0430",
                                "label" => "u0414u0430u0442u0430 u0432u044bu043bu0435u0442u0430",
                                "value" => "05/01/2016"
                            ), array(
                                "key" => "u0414u0430u0442u0430 u0432u043eu0437u0432u0440u0430u0442u0430",
                                "label" => "u0414u0430u0442u0430 u0432u043eu0437u0432u0440u0430u0442u0430",
                                "value" => "12/01/2016"
                            ), array(
                                "key" => "u041fu0440u0438u043au0440u0435u043fu0438u0442u0435 u043au043eu043fu0438u044e u043fu0430u0441u043fu043eu0440u0442u0430 u0438 u0444u043eu0442u043e (u0434u043bu044f u0432u0441u0435u0445 u0442u0443u0440u0438u0441u0442u043eu0432)",
                                "label" => "u041fu0440u0438u043au0440u0435u043fu0438u0442u0435 u043au043eu043fu0438u044e u043fu0430u0441u043fu043eu0440u0442u0430 u0438 u0444u043eu0442u043e (u0434u043bu044f u0432u0441u0435u0445 u0442u0443u0440u0438u0441u0442u043eu0432)",
                                "value" => "asanova-lenura-05jan-krr_1.pdf,ignateva-diana-05jan-krr_1.pdf,ignateva-eva-05jan-krr_1.pdf,ignateva-milena-05jan-krr_1.pdf,ignateva-simona-05jan-krr_1.pdf,ignatyev-mark-05jan-krr_1.pdf,ignatyeva-tatiana-05jan-krr_1.pdf,u041bu0435u043du0443u0440u0430-1.jpg,u0410u0441u0430u043du043eu0432u0430-u041bu0435u043du0443u0440u0430-u0428u0435u0432u043au0435u0442u043eu0432u043du0430-u0437u0430u0433u0440u0430u043d-1.jpeg"
                            ))
                    )),
                "shipping_lines" => array(),
                "tax_lines" => array(),
                "fee_lines" => array(),
                "coupon_lines" => array(),
                "customer" => array(
                    "id" => 0,
                    "email" => "ridos@yandex.ru",
                    "first_name" => "u041cu0430u0440u043a",
                    "last_name" => "u0418u0433u043du0430u0442u044cu0435u0432",
                    "billing_address" => array(
                        "first_name" => "u041cu0430u0440u043a",
                        "last_name" => "u0418u0433u043du0430u0442u044cu0435u0432",
                        "company" => "",
                        "address_1" => "",
                        "address_2" => "",
                        "city" => "",
                        "state" => "",
                        "postcode" => "",
                        "country" => "",
                        "email" => "ridos@yandex.ru",
                        "phone" => "+79882481548"
                    ),
                    "shipping_address" => array(
                        "first_name" => "u041cu0430u0440u043a",
                        "last_name" => "u0418u0433u043du0430u0442u044cu0435u0432",
                        "company" => "",
                        "address_1" => "",
                        "address_2" => "",
                        "city" => "",
                        "state" => "",
                        "postcode" => "",
                        "country" => ""
                    )
                )
            )
        );

        if ($validRequest) {
            $response = new Response();
        } else {
            $response = new Response('', 404);
        }
        return $response;
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

        $entity->setHeader(json_decode($entity->getHeader()));
        $entity->setContent(json_decode($entity->getContent()));

        return array(
            'entity' => $entity,
        );
    }

}
