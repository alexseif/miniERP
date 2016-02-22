<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\Orders;
use MeVisa\ERPBundle\Entity\Invoices;
use MeVisa\ERPBundle\Form\OrdersType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        return array(
            'orders' => $this->get('erp.order')->getOrdersList(),
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
        $order = $this->get('erp.order')->getOrder($id);

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
     * Displays a form to create a new Orders entity.
     *
     * @Route("/new", name="orders_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $order = new Orders();
        $order->setState('backoffice');
        $order->setChannel('POS');
        $order->setCreatedAt(new \DateTime("now"));

        $form = $this->createForm(new OrdersType(), $order, array(
            'action' => $this->generateUrl('orders_new'),
            'method' => 'POST',
        ));

        $form->add('save', 'submit', array(
            'label' => null,
            'attr' => array(
                'class' => 'btn-success pull-right'
        )));

        $form->handleRequest($request);
// TODO: State buttons
//        foreach ($order->getOrderState()->getCurrentState()->getChildren() as $state) {
//            $form->add($state->getKey(), 'submit', array('attr' => array('class' => 'btn-toolbar btn-' . $state->getBootstrapClass())
//            ));
//        }
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->get('erp.order')->createNewPOSOrder($order);

                return $this->redirect($this->generateUrl('orders_show', array('id' => $order->getId())));
            } else {
                foreach ($form->getErrors() as $error) {
                    $this->addFlash('error', $error);
                }
            }
        }

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
     * Customer intellicense 
     *
     * @Route("/select_customer", name="select_customer")
     * @Method("GET")
     * @Template()
     */
    public function selectCustomerAction(Request $request)
    {
        $term = $request->get('term');

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('MeVisaCRMBundle:Customers')->findLikeName($term);

        return new JsonResponse($entities);

        return array(
            'entities' => $entities,
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
        $statusForm = $this->createStatusForm($order);

        $orderDocuments = $order->getOrderDocuments();
        foreach ($orderDocuments as $document) {
            if (0 === strpos($document->getPath(), 'http://www.mevisa.ru/')) {
                $parts = explode('/', $document->getPath());
                $parts[count($parts) - 2] = 'thumbs';
                $parts[count($parts) - 1] = $document->getName();
                $document->thumbnail = implode('/', $parts);
            } else {
                $document->thumbnail = false;
                $document->setPath($this->get('request')->getScheme() . '://' . $this->get('request')->getHttpHost() . $this->get('request')->getBasePath() . '/' . $document->getWebPath());
            }
        }

        return array(
            'order' => $order,
            'logs' => $this->get('erp.order')->getOrderLog($id),
            'documents' => $orderDocuments,
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
        $statusForm = $this->createStatusForm($order);

        $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        $orderDocuments = $order->getOrderDocuments();
        foreach ($orderDocuments as $document) {
            if (0 === strpos($document->getPath(), 'http://www.mevisa.ru/')) {
                $parts = explode('/', $document->getPath());
                $parts[count($parts) - 2] = 'thumbs';
                $parts[count($parts) - 1] = $document->getName();
                $document->thumbnail = implode('/', $parts);
            } else {
                $document->thumbnail = false;
                $document->setPath($this->get('request')->getScheme() . '://' . $this->get('request')->getHttpHost() . $this->get('request')->getBasePath() . '/' . $document->getWebPath());
            }
        }
        return array(
            'order' => $order,
            'productPrices' => $productPrices,
            'documents' => $orderDocuments,
            'logs' => $this->get('erp.order')->getOrderLog($id),
            'form' => $editForm->createView(),
            'status_form' => $statusForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Orders entity.
     *
     * @Route("/{id}/companions", name="orders_edit_companions")
     * @Method("GET")
     * @Template()
     */
    public function editCompanionsAction($id)
    {
        $em = $this->getDoctrine()->getManager();


        $order = $em->getRepository('MeVisaERPBundle:Orders')->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Unable to find Orders entity.');
        }

        $state = $order->getState();
        $order->startOrderStateEnginge();
        $order->setState($state);


        $editForm = $this->createEditForm($order);
        $statusForm = $this->createStatusForm($order);

        $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        $orderDocuments = $order->getOrderDocuments();
        foreach ($orderDocuments as $document) {
            if (0 === strpos($document->getPath(), 'http://www.mevisa.ru/')) {
                $parts = explode('/', $document->getPath());
                $parts[count($parts) - 2] = 'thumbs';
                $parts[count($parts) - 1] = $document->getName();
                $document->thumbnail = implode('/', $parts);
            } else {
                $document->thumbnail = false;
                $document->setPath($this->get('request')->getScheme() . '://' . $this->get('request')->getHttpHost() . $this->get('request')->getBasePath() . '/' . $document->getWebPath());
            }
        }
        return array(
            'order' => $order,
            'productPrices' => $productPrices,
            'documents' => $orderDocuments,
            'logs' => $this->get('erp.order')->getOrderLog($id),
            'form' => $editForm->createView(),
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

        $editForm = $this->createEditForm($order);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $this->get('erp.order')->saveOrder($order);

            return $this->redirect($this->generateUrl('orders_show', array('id' => $order->getId())));
        } else {
            foreach ($form->getErrors() as $error) {
                $this->addFlash('error', $error);
            }
        }

        $statusForm = $this->createStatusForm($order);
        $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        $orderDocuments = $order->getOrderDocuments();
        foreach ($orderDocuments as $document) {
            if (0 === strpos($document->getPath(), 'http://www.mevisa.ru/')) {
                $parts = explode('/', $document->getPath());
                $parts[count($parts) - 2] = 'thumbs';
                $parts[count($parts) - 1] = $document->getName();
                $document->thumbnail = implode('/', $parts);
            } else {
                $document->thumbnail = false;
                $document->setPath($this->get('request')->getScheme() . '://' . $this->get('request')->getHttpHost() . $this->get('request')->getBasePath() . '/' . $document->getWebPath());
            }
        }

        return array(
            'order' => $order,
            'productPrices' => $productPrices,
            'logs' => $this->get('erp.order')->getOrderLog($id),
            'form' => $editForm->createView(),
            'status_form' => $statusForm->createView(),
        );
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
            'attr' => array('class' => 'pull-right btn-default')
        ));

        return $form->getForm();
    }

    /**
     * Creates a form to update a Orders Status entity.
     *
     * @param Orders $order The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createStatusForm(Orders $order)
    {
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('orders_status_update', array('id' => $order->getId())))
                ->setMethod('PUT');
        $children = $order->getOrderState()->getCurrentState()->getChildren();
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

        $statusForm = $this->createStatusForm($order);

        $statusForm->handleRequest($request);

        if ($statusForm->isValid()) {

            $iterator = $statusForm->getIterator();
            foreach ($iterator as $key => $value) {
                if ($value->isClicked()) {
                    $order->setState($key);
                }
            }
            if ("approved" == $order->getState() || "rejected" == $order->getState()) {
                $order->setCompletedAt(new \DateTime());
            }

            if (empty($order->getUpdatedAt())) {
                $order->setUpdatedAt(new \DateTime());
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
        );
    }

    /**
     * @Route("/{id}/uploads", )
     * @Template()
     */
    public function uploadAction(Request $request, $id)
    {
        $form = $this->createForm(new \MeVisa\ERPBundle\Form\OrderDocumentsType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
        }

        return array('form' => $form->createView());
    }

    /**
     * Sends approval.
     *
     * @Route("/{id}/approval", name="order_approval")
     * @Method("GET")
     * @Template()
     */
    public function approvalAction($id)
    {
        $message = \Swift_Message::newInstance()
                ->setSubject('Hello Email')
                ->setFrom('zakaz@mevisa.ru')
                ->setTo('alex.seif@gmail.com')
                ->setBody(
                $this->renderView(
                        'MeVisaERPBundle:Orders:email.html.twig', array()
                ), 'text/html'
        );
        $result = $this->get('mailer')->send($message);

        $this->addFlash('success', 'Approval sent');
//        return $this->redirect($this->generateUrl('orders_show',
//                    array('id' => $id)));
        return array('result' => $result);
    }

    /**
     * Action to generate pdf Invoice.
     *
     * @Route("/{id}/invoicepdf", name="order_generate_pdf")
     * @Method("GET")
     */
    public function invoicepdfAction($id)
    {
        $this->get('erp.order')->generateInvoice($id);

        $this->addFlash('success', 'invoice generated');
        return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
    }

}
