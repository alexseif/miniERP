<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\Orders;
use MeVisa\ERPBundle\Form\OrdersType;
use Knp\Snappy\Pdf;

/**
 * Orders controller.
 *
 * @Route("/orders")
 */
class OrdersController extends Controller
{

    /**
     * Lists all Orders entities.
     *
     * @Route("/", name="orders")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:Orders')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Orders entity.
     *
     * @Route("/", name="orders_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:Orders:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $order = new Orders();
        $order->setState('backoffice');

        $form = $this->createCreateForm($order);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {

            $orderNo = $em->getRepository('MeVisaERPBundle:Orders')->queryLastPOSNumber();
            if ($orderNo) {
                $number = ltrim($orderNo->getNumber(), 'POS');
                $order->setNumber('POS' . ($number + 1));
            } else {
                $order->setNumber('POS1');
            }

            // TODO: State machine
//            $order->setState('backoffice');
            // TODO: Order Channel
            $order->setChannel('POS');

            $customer = $order->getCustomer();
            if (!$customer->getId()) {
                $em->persist($customer);
                // TODO: Check new Customer
                // TODO: add new customer
            }

            // $customerCheck = $em->getRepository('MeVisaCRMBundle:Customer')->find($order->getCustomer()->getId());
            $customerCheck = true;
            if (!$customerCheck) {
                //  echo "Still no Customer <br/>";
            }

            $orderProducts = $order->getOrderProducts();

            // $orderProductCheck = false;
//            $orderProductTotal = 0;
            foreach ($orderProducts as $orderProduct) {
                // TODO: Check Order Product
                // TODO: Handle no proper products or disabled
//                $product = $orderProduct->getProduct();
//                $productPrice = $product->getPricing();
//                $orderProduct->setUnitPrice($productPrice[0]->getPrice());
//                $orderProduct->setTotal($productPrice[0]->getPrice() * $orderProduct->getQuantity());
//                $orderProductTotal += $orderProduct->getTotal();
                $order->addOrderProduct($orderProduct);
                // $em->merge($orderProduct);
            }
//            $order->setProductsTotal($orderProductTotal);
//            $order->setTotal($order->getProductsTotal() + $order->getAdjustmentTotal());

            $order->setCreatedAt(new \DateTime("now"));

            $orderCompanions = $order->getOrderCompanions();
            // TODO: Check Order Companions
            foreach ($orderCompanions as $companion) {
                $order->addOrderCompanion($companion);
            }

            // TODO: Check Order Comments
            // TODO: if order comment is not empty add new orderComment
            $orderComments = $order->getOrderComments();
            foreach ($orderComments as $comment) {
                if ("" == $comment->getComment()) {
                    $order->removeOrderComment($comment);
                } else {
                    $this->getUser()->addComment($comment);
                    $comment->setCreatedAt(new \DateTime());
                    $order->addOrderComment($comment);
                }
            }

            $orderPayments = $order->getOrderPayments();
            foreach ($orderPayments as $payment) {
                $payment->setCreatedAt(new \DateTime());
                $payment->setState("paid");
                $order->addOrderPayment($payment);
            }

            // TODO: Check Order
            // TODO: Upload OrderDocuments then presist
            $orderDocuments = $order->getOrderDocuments();
            foreach ($orderDocuments as $document) {
                $order->addOrderDocument($document);
            }

            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('orders_show', array('id' => $order->getId())));
        } else {
            return array(
                "errors" => $form->getErrors()
            );
        }

        return array(
            'order' => $order,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Orders entity.
     *
     * @Route("/{id}/comment", name="orders_comments_new")
     * @Method("POST")
     */
    public function createCommentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }

        $orderComment = new \MeVisa\ERPBundle\Entity\OrderComments();
        $orderComment->setOrderRef($id);
        $form = $this->createCommentForm($orderComment);
        $form->handleRequest($request);



        if ($form->isValid()) {
            $comment = $form->getData();
            if ("" != $comment['comment']) {
                $orderComment->setComment($comment['comment']);
                $orderComment->setCreatedAt(new \DateTime());
                $this->getUser()->addComment($orderComment);
                $order->addOrderComment($orderComment);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
    }

    /**
     * Creates a form to create a Orders entity.
     *
     * @param Orders $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Orders $entity)
    {
        $form = $this->createForm(new OrdersType(), $entity, array(
            'action' => $this->generateUrl('orders_create'),
            'method' => 'POST',
        ));

        $form->add('save', 'submit', array(
            'label' => null,
            'attr' => array(
                'class' => 'btn-success pull-right'
        )));

        return $form;
    }

    /**
     * Displays a form to create a new Orders entity.
     *
     * @Route("/new", name="orders_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $order = new Orders();
        // Set initial state
        $order->setState('backoffice');
        $order->setChannel('pos');

        $form = $this->createCreateForm($order);

//        foreach ($order->getOrderState()->getCurrentState()->getChildren() as $state) {
//            $form->add($state->getKey(), 'submit', array('attr' => array('class' => 'btn-toolbar btn-' . $state->getBootstrapClass())
//            ));
//        }

        $em = $this->getDoctrine()->getManager();
        //FIXME: Select with invalid product_prices produces errors
        $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        return array(
            'order' => $order,
            'productPrices' => $productPrices,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Orders entity.
     *
     * @Route("/{id}", name="orders_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }
        $state = $order->getState();
        $order->startOrderStateEnginge();
        $order->setOrderState($state);

        $orderComment = new \MeVisa\ERPBundle\Entity\OrderComments();
        $orderComment->setOrderRef($order->getId());
        $commentForm = $this->createCommentForm($orderComment);
        $deleteForm = $this->createDeleteForm($id);
        $statusForm = $this->createStatusForm($order);

        return array(
            'order' => $order,
            'delete_form' => $deleteForm->createView(),
            'status_form' => $statusForm->createView(),
            'comment_form' => $commentForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Orders entity.
     *
     * @Route("/{id}/edit", name="orders_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();


        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

        $state = $order->getState();
        $order->startOrderStateEnginge();
        $order->setState($state);

        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }

        $editForm = $this->createEditForm($order);
        $deleteForm = $this->createDeleteForm($id);
        $statusForm = $this->createStatusForm($order);

        $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        return array(
            'order' => $order,
            'productPrices' => $productPrices,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'status_form' => $statusForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Orders entity.
     *
     * @param Orders $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Orders $entity)
    {
        $form = $this->createForm(new OrdersType(), $entity, array(
            'action' => $this->generateUrl('orders_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('update', 'submit', array(
            'label' => null,
            'attr' => array(
                'class' => 'btn-success pull-right'
        )));


        return $form;
    }

    /**
     * Edits an existing Orders entity.
     *
     * @Route("/{id}", name="orders_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:Orders:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }

        $state = $order->getState();
        $order->startOrderStateEnginge();
        $order->setState($state);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($order);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $orderCompanions = $order->getOrderCompanions();
            // TODO: Check Order Companions
            foreach ($orderCompanions as $companion) {
                $order->addOrderCompanion($companion);
            }

            $orderProducts = $order->getOrderProducts();
            foreach ($orderProducts as $orderProduct) {
                $order->addOrderProduct($orderProduct);
            }

            $orderComments = $order->getOrderComments();
            foreach ($orderComments as $comment) {
                if ("" == $comment->getComment()) {
                    $order->removeOrderComment($comment);
                } else {
                    $this->getUser()->addComment($comment);
                    $comment->setCreatedAt(new \DateTime());
                    $order->addOrderComment($comment);
                }
            }
            $orderDocuments = $order->getOrderDocuments();
            foreach ($orderDocuments as $document) {
                $order->addOrderDocument($document);
            }

            $order->setUpdatedAt(new \DateTime());

            $em->flush();

            return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
        } else {
            echo "Form not valid becuase:<br/>";
            die();
            $formErrors = $editForm->getErrors();
        }

        $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        return array(
            'order' => $order,
            'productPrices' => $productPrices,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Orders entity.
     *
     * @Route("/{id}", name="orders_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Orders entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orders'));
    }

    /**
     * Creates a form to delete a Orders entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('orders_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    /**
     * Creates a form to update a Orders Status entity.
     *
     * @param Orders $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCommentForm(\MeVisa\ERPBundle\Entity\OrderComments $entity)
    {
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('orders_comments_new', array('id' => $entity->getOrderRef())))
                ->setMethod('POST');

        $form->add('comment', 'textarea', array(
            'data' => $entity->getComment(),
            'required' => true,
            'label' => false
        ));
        $form->add('save', 'submit', array(
            'label' => false,
            'attr' => array('class' => 'pull-right')
        ));

        return $form->getForm();
    }

    /**
     * Creates a form to update a Orders Status entity.
     *
     * @param Orders $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createStatusForm(Orders $entity)
    {
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('orders_status_update', array('id' => $entity->getId())))
                ->setMethod('PUT');
        $children = $entity->getOrderState()->getCurrentState()->getChildren();
        if (is_array($children)) {
            foreach ($children as $key => $child) {
                $form->add($child->getKey(), 'submit', array(
                    'label' => $child->getName(),
                    'attr' => array(
                        'id' => 'state_' . $key,
                        'class' => 'ml-5 btn-group btn-' . $child->getBootstrapClass(),
                        'value' => $child->getKey(),
                )));
            }
        }

        return $form->getForm();
    }

    /**
     * Edits an existing Orders entity.
     *
     * @Route("/{id}/status", name="orders_status_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:Orders:show.html.twig")
     */
    public function updateStateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }

        $state = $order->getState();
        $order->startOrderStateEnginge();
        $order->setState($state);

        $deleteForm = $this->createDeleteForm($id);
        $statusForm = $this->createStatusForm($order);

        $statusForm->handleRequest($request);

        if ($statusForm->isValid()) {

            $iterator = $statusForm->getIterator();
            foreach ($iterator as $key => $value) {
                if ($value->isClicked()) {
                    $order->setState($key);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
        } else {
            echo "Form not valid becuase:<br/>";
            $formErrors = $statusForm->getErrors();
        }

        $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        return array(
            'order' => $order,
            'productPrices' => $productPrices,
            'status_form' => $statusForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays a Orders entity.
     *
     * @Route("/invoice/{id}", name="order_show_invoice")
     * @Method("GET")
     * @Template()
     */
    public function invoiceAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }
        $state = $order->getState();
        $order->startOrderStateEnginge();
        $order->setOrderState($state);

        $deleteForm = $this->createDeleteForm($id);
        $statusForm = $this->createStatusForm($order);

        $myProjectDirectory = __DIR__ . '/../../../../';
//        $myProjectDirectory = '/srv/www/mevisa/';



        $snappy = new Pdf($myProjectDirectory . 'vendor/h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386');

        $snappy->setOption('title', 'MeVisa Invoice');
        $snappy->setOption('encoding', 'UTF');

        $snappy->generateFromHtml($this->renderView(
                        'MeVisaERPBundle:Orders:invoicepdf.html.twig', array(
                    'order' => $order,
                        )
                ), $myProjectDirectory . 'web/invoices/file.pdf', array(), true);

//        $this->get('knp_snappy.pdf')->generateFromHtml(
//                $this->renderView(
//                        'MeVisaERPBundle:Orders:invoice.html.twig', array(
//                    'order' => $order,
//                        )
//                ), '/web/test/file.pdf'
//        );

        return array(
            'order' => $order,
        );
    }

    /**
     * Finds and displays a Orders entity.
     *
     * @Route("/invoice/{id}/pdf", name="order_show_pdf")
     * @Method("GET")
     * @Template()
     */
    public function invoicepdfAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);

        return array(
            'order' => $order,
        );
    }

    /**
     * @Route("/{id}/uploads", )
     * @Template()
     */
    public function uploadAction(Request $request, $id)
    {
        $form = $this->createForm(new \MeVisa\ERPBundle\Form\OrderDocumentsType());
//        $form->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
        }

        return array('form' => $form->createView());
    }

}
