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
   * Lists all Archived Orders entities.
   *
   * @Route("/archive", name="orders_archive")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Orders:index.html.twig")
   */
  public function archiveListAction()
  {
    return array(
      'orders' => $this->get('erp.order')->getArchivedOrdersList(),
    );
  }

  /**
   * Lists all Deleted Orders entities.
   *
   * @Route("/delete", name="orders_delete")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Orders:index.html.twig")
   */
  public function deleteListAction()
  {
    return array(
      'orders' => $this->get('erp.order')->getDeletedOrdersList(),
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
    $order->setChannel('POS');
    $order->setState('backoffice');
    $order->setCreatedAt(new \DateTime("now"));
    //Generate Form
    $form = $this->createForm(new OrdersType(), $order, array(
      'action' => $this->generateUrl('orders_new'),
      'method' => 'POST',
    ));
    $form->add('save', 'submit', array(
      'label' => null,
      'attr' => array(
        'class' => 'btn-success pull-right'
      )
    ));

    //Handle Form
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        //Validations
        // Arrival & Departure
        // Post & arrival
        // at least1 Order Product
        $orderProducts = $order->getOrderProducts();
        if ($orderProducts->count() < 1) {
          $this->addFlash('error', 'Order must have at least 1 product');
        }

        // 1 Person travelling

        $this->get('erp.order')->createNewPOSOrder($order);

        return $this->redirect($this->generateUrl('orders_show', array('id' => $order->getId())));
      }
    }

    return array(
      'order' => $order,
      'productPrices' => $this->getAvailableProducts(),
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
    $order = $this->get('erp.order')->getOrder($id);

    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $companion_form = null;
    if ("post" == $order->getState()) {
      $companion_form = $this->createCompanionSatausForm($order)->createView();
    }
    $orderComment = new \MeVisa\ERPBundle\Entity\OrderComments();
    $orderComment->setOrderRef($order->getId());

    return array(
      'order' => $order,
      'logs' => $this->get('erp.order')->getOrderLog($id),
      'documents' => $this->getThumbnails($order),
      'status_form' => $this->createStatusForm($order)->createView(),
      'comment_form' => $this->createCommentForm($orderComment)->createView(),
      'companions_form' => $companion_form,
    );
  }

  /**
   * Displays a form to edit an existing Orders entity.
   *
   * @Route("/{id}/edit", name="orders_edit")
   * @Method({"GET", "PUT"})
   * @Template()
   */
  public function editAction($id, Request $request)
  {
    $order = $this->get('erp.order')->getOrder($id);
    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $form = $this->createEditForm($order);
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $this->get('erp.order')->updateOrder($order);
        // TODO: Validations
        // Check Order Product
        //  Handle no proper products or disabled
        return $this->redirect($this->generateUrl('orders_show', array('id' => $order->getId())));
      }
    }

    $statusForm = $this->createStatusForm($order);


    return array(
      'order' => $order,
      'productPrices' => $this->getAvailableProducts(),
      'documents' => $this->getThumbnails($order),
      'logs' => $this->get('erp.order')->getOrderLog($id),
      'form' => $form->createView(),
      'status_form' => $statusForm->createView(),
    );
  }

  /**
   * Soft delete Orders entity.
   *
   * @Route("/{id}/soft-delete", name="orders_soft_delete")
   * @Method({"GET", "PUT"})
   */
  public function softDeleteAction($id, Request $request)
  {
    $order = $this->get('erp.order')->getOrder($id);
    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }
    $this->get('erp.order')->softDeleteOrder($order);
    return $this->redirect($this->generateUrl('orders'));
  }
  /**
   * Soft delete Orders entity.
   *
   * @Route("/{id}/hard-delete", name="orders_hard_delete")
   * @Method({"GET", "PUT"})
   */
  public function hardDeleteAction($id, Request $request)
  {
    $order = $this->get('erp.order')->getOrder($id);
    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }
    $this->get('erp.order')->hardDeleteOrder($order);
    return $this->redirect($this->generateUrl('orders_delete'));
  }

  /**
   * Displays a form to edit an existing Orders entity.
   *
   * @Route("/{id}/companions", name="orders_edit_companions")
   * @Method({"GET", "PUT"})
   * @Template()
   */
  public function editCompanionsAction($id, Request $request)
  {
    $order = $this->get('erp.order')->getOrder($id);
    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $form = $this->createForm(new \MeVisa\ERPBundle\Form\OrdersCompanionsType(), $order, array(
      'action' => $this->generateUrl('orders_edit_companions', array('id' => $order->getId())),
      'method' => 'PUT',
    ));

    $form->add('update', 'submit', array(
      'label' => null,
      'attr' => array(
        'class' => 'btn-success pull-right'
    )));


    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $this->get('erp.order')->updateOrder($order);

        return $this->redirect($this->generateUrl('orders_show', array('id' => $order->getId())));
      }
    }

    return array(
      'order' => $order,
      'documents' => $this->getThumbnails($order),
      'logs' => $this->get('erp.order')->getOrderLog($id),
      'form' => $form->createView(),
    );
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
    $order = $this->get('erp.order')->getOrder($id);

    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $statusForm = $this->createStatusForm($order);

    $statusForm->handleRequest($request);

    if ($statusForm->isSubmitted()) {
      if ($statusForm->isValid()) {

        $order->setState($statusForm->getClickedButton()->getName());

        if (empty($order->getUpdatedAt())) {
          $order->setUpdatedAt(new \DateTime());
        }
        $this->get('erp.order')->stateEffect($order);


        $em->flush();
        return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
      }
    }

    return array(
      'order' => $order,
      'status_form' => $statusForm->createView(),
    );
  }

  /**
   * Edits an existing Orders entity.
   *
   * @Route("/{id}/companion_status", name="orders_companion_status_update")
   * @Method("PUT")
   * @Template("MeVisaERPBundle:Orders:show.html.twig")
   */
  public function updateCompanionStateAction(Request $request, $id)
  {
    $order = $this->get('erp.order')->getOrder($id);
    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }
    $em = $this->getDoctrine()->getManager();

    $companionForm = $this->createCompanionSatausForm($order);
    $companionForm->handleRequest($request);

    if ($companionForm->isSubmitted()) {
      if ($companionForm->isValid()) {
        if (empty($order->getUpdatedAt())) {
          $order->setUpdatedAt(new \DateTime());
        }
        $orderComplete = true;
        $companions = $order->getOrderCompanions();
        foreach ($companions as $companion) {
          if (!$companion->getState()) {
            $orderComplete = false;
          }
        }
        if ($orderComplete) {
          $order->setState($order->getOrderCompanions()->first()->getState());
        }
        $this->get('erp.order')->stateEffect($order);

        $em->flush();
        return $this->redirect($this->generateUrl('orders_show', array('id' => $id)));
      }
    }

    $orderComment = new \MeVisa\ERPBundle\Entity\OrderComments();
    $orderComment->setOrderRef($order->getId());

    return array(
      'order' => $order,
      'logs' => $this->get('erp.order')->getOrderLog($id),
      'documents' => $this->getThumbnails($order),
      'status_form' => $this->createStatusForm($order)->createView(),
      'comment_form' => $this->createCommentForm($orderComment)->createView(),
      'companions_form' => $companionForm->createView(),
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

  /**
   * Action to Preview Invoice.
   *
   * @Route("/{id}/invoice_preview", name="order_invoice_preview")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Orders:pdfinvoice.html.twig")
   */
  public function invoicePreviewAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $order = $this->get('erp.order')->getOrder($id);

    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $CompanySettings = $em->getRepository('MeVisaERPBundle:CompanySettings')->find(1);
    $products = $order->getOrderProducts();
    $invoice = new Invoices();
    $productsLine = array();
    foreach ($products as $product) {
      $productsLine[] = $product->getProduct()->getName();
    }
    $productsLine = implode(',', $productsLine);

    return array(
      'order' => $order,
      'productsLine' => $productsLine,
      'invoice' => $invoice,
      'companySettings' => $CompanySettings
    );
  }

  /**
   * Action to Preview Invoice.
   *
   * @Route("/{id}/agreement_preview", name="order_agreement_preview")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Orders:pdfagreement.html.twig")
   */
  public function agreementPreviewAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $order = $this->get('erp.order')->getOrder($id);

    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $CompanySettings = $em->getRepository('MeVisaERPBundle:CompanySettings')->find(1);
    $products = $order->getOrderProducts();
    $invoice = new Invoices();
    $productsLine = array();
    foreach ($products as $product) {
      $productsLine[] = $product->getProduct()->getName();
    }
    $productsLine = implode(',', $productsLine);

    return array(
      'order' => $order,
      'productsLine' => $productsLine,
      'invoice' => $invoice,
      'companySettings' => $CompanySettings
    );
  }

  /**
   * Action to Preview Invoice.
   *
   * @Route("/{id}/waiver_preview", name="order_waiver_preview")
   * @Method("GET")
   * @Template("MeVisaERPBundle:Orders:pdfwaiver.html.twig")
   */
  public function waiverPreviewAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $order = $this->get('erp.order')->getOrder($id);

    if (!$order) {
      throw $this->createNotFoundException('Unable to find Orders entity.');
    }

    $CompanySettings = $em->getRepository('MeVisaERPBundle:CompanySettings')->find(1);
    $products = $order->getOrderProducts();
    $invoice = new Invoices();
    $productsLine = array();
    foreach ($products as $product) {
      $productsLine[] = $product->getProduct()->getName();
    }
    $productsLine = implode(',', $productsLine);

    return array(
      'order' => $order,
      'productsLine' => $productsLine,
      'invoice' => $invoice,
      'companySettings' => $CompanySettings
    );
  }

  private function getThumbnails($order)
  {
    $orderDocuments = $order->getOrderDocuments();
    foreach ($orderDocuments as $document) {
      if (0 === strpos($document->getPath(), 'http://mevisa.ru/')) {
        $parts = explode('/', $document->getPath());
        $parts[count($parts) - 2] = 'thumbs';
        $parts[count($parts) - 1] = $document->getName();
        $document->thumbnail = implode('/', $parts);
      } elseif (0 === strpos($document->getPath(), 'http://uaevisa.ru/')) {
        $document->thumbnail = false;
      } else {
        $document->thumbnail = false;
        $document->setPath($this->get('request')->getScheme() . '://' . $this->get('request')->getHttpHost() . $this->get('request')->getBasePath() . '/' . $document->getWebPath());
      }
    }
    return $orderDocuments;
  }

  private function getAvailableProducts()
  {
    $em = $this->getDoctrine()->getManager();
    //FIXME: Select with invalid product_prices produces errors

    $productPrices = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAllPrices();
    return $productPrices;
  }

  private function isPostable($id)
  {
    $order = $this->get('erp.order')->getOrder($id);
    if (!empty($order->getCompletedAt())) {
      return true;
    }
    if (count($order->getOrderCompanions()) != $order->getPeople()) {
      return false;
    }
    $orderPayment = $order->getOrderPayments()->last();
    if ("paid" == $orderPayment->getState()) {
      return true;
    }
    return false;
  }

  private function isInvoicable($id)
  {
    $order = $this->get('erp.order')->getOrder($id);
    return true;
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

    $children = $order->getOrderState()->getAvailableStates();
    $companionsCount = ($order->getOrderCompanions()->count() > 1) ? true : false;
    if (is_array($children)) {
      $postable = $this->isPostable($order->getId());
      foreach ($children as $key => $child) {
        if ("post" == $child->getKey() && false == $postable) {
          unset($child);
          continue;
        }
        if (("approved" == $child->getKey() || "rejected" == $child->getKey()) && $companionsCount) {
          $form->add($child->getKey(), 'button', array(
            'label' => $child->getName(),
            'attr' => array(
              'id' => 'state_' . $key,
              'class' => 'ml-5 btn-group btn-' . $child->getBootstrapClass(),
              'value' => $child->getKey(),
              'data-toggle' => "modal",
              'data-target' => "#approvalModal"
          )));
        } else {
          $form->add($child->getKey(), 'submit', array(
            'label' => $child->getName(),
            'attr' => array(
              'id' => 'state_' . $key,
              'class' => 'ml-5 btn-group btn-' . $child->getBootstrapClass(),
              'value' => $child->getKey(),
          )));
        }
      }
    }

    return $form->getForm();
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
   * Creates a form to edit a Orders entity.
   *
   * @param Orders $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createEditForm(Orders $entity)
  {
    $form = $this->createForm(new OrdersType(), $entity, array(
      'action' => $this->generateUrl('orders_edit', array('id' => $entity->getId())),
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
   * Creates a form to update a Orders companions status.
   *
   * @param Orders $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createCompanionSatausForm(Orders $order)
  {
    $companion_form_builder = $this->createFormBuilder($order)
        ->setAction($this->generateUrl('orders_companion_status_update', array('id' => $order->getId())))
        ->setMethod('PUT');

    $companion_form_builder->add('orderCompanions', 'collection', array(
      'type' => new \MeVisa\ERPBundle\Form\OrderCompanionStateType(),
      'allow_add' => false,
      'allow_delete' => false,
      'label' => false,
    ));
    $companion_form_builder->add('save', 'submit', array(
      'label' => null,
      'attr' => array(
        'class' => 'btn-success pull-right'
      )
    ));

    return $companion_form_builder->getForm();
  }

}
