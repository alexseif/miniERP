<?php

namespace MeVisa\ERPBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class OrderService
{

  protected $em;
  protected $templating;
  protected $securityContext;

  public function __construct(EntityManager $em, EngineInterface $templating, \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $securityContext)
  {
    $this->em = $em;
    $this->templating = $templating;
    $this->securityContext = $securityContext;
  }

  public function getOrdersList()
  {
    return $this->em->getRepository('MeVisaERPBundle:Orders')->findCurrentOrdersList();
  }

  public function getArchivedOrdersList()
  {
    return $this->em->getRepository('MeVisaERPBundle:Orders')->findArchivedOrdersList();
  }

  public function getDeletedOrdersList()
  {
    return $this->em->getRepository('MeVisaERPBundle:Orders')->findDeletedOrdersList();
  }

  public function getOrder($id)
  {
    $order = $this->em->getRepository('MeVisaERPBundle:Orders')->find($id);

    if (!$order) {
      return null;
    }
    $state = $order->getState();
    $order->startOrderStateEnginge();
    $order->setOrderState($state);
    return $order;
  }

  public function getOrderLog($id)
  {
    $logRepo = $this->em->getRepository('Gedmo\Loggable\Entity\LogEntry');
    $orderLog = $this->em->find('MeVisa\ERPBundle\Entity\Orders', $id);
    return $logRepo->getLogEntries($orderLog);
  }

  public function generateNewPOSNumber()
  {
    $POSNumber = '';
    $lastPOSOrder = $this->em->getRepository('MeVisaERPBundle:Orders')->queryLastPOSOrder();
    $lastPOSNo = ltrim($lastPOSOrder->getNumber(), 'POS');
    if ($lastPOSOrder) {
      $lastPOSNo = ltrim($lastPOSOrder->getNumber(), 'POS');
      $POSNumber = 'POS' . ($lastPOSNo + 1);
    } else {
      $POSNumber = 'POS1';
    }
    return $POSNumber;
  }

  public function createNewPOSOrder($order)
  {
    $order->setNumber($this->generateNewPOSNumber());

    $this->setOrderDetails($order);
    $this->em->persist($order);
    $this->em->flush();
  }

  public function updateOrder($order)
  {
    $this->setOrderDetails($order);
    if (empty($order->getUpdatedAt())) {
      $order->setUpdatedAt(new \DateTime());
    }
    $this->em->flush();
  }

  public function softDeleteOrder($order)
  {
    $order->setState('deleted');
    $order = $this->stateEffect($order);
    $this->em->flush();
  }

  public function hardDeleteOrder($order)
  {
    $this->em->remove($order);
    $this->em->flush();
  }

  public function generateInvoice($id)
  {
    $order = $this->getOrder($id);
    $CompanySettings = $this->em->getRepository('MeVisaERPBundle:CompanySettings')->find(1);

    $invoice = new \MeVisa\ERPBundle\Entity\Invoices();
    $invoice->setCreatedAt(new \DateTime());
    $order->addInvoice($invoice);
    $this->em->persist($invoice);
    $this->em->flush();

    $products = $order->getOrderProducts();
    $productsLine = array();
    foreach ($products as $product) {
      $productsLine[] = $product->getProduct()->getName();
    }
    $productsLine = implode(',', $productsLine);

    $this->em->flush();
  }

  public function setCustomer($order)
  {
    // Customer Exists By ID
    if (empty($order->getCustomer()->getId())) {
      // Customer Exists By Email
      $customer = $this->em->getRepository('MeVisaCRMBundle:Customers')->findOneBy(array("email" => $order->getCustomer()->getEmail()));
      if ($customer) {
        if ($customer->getName() != $order->getCustomer()->getName()) {
          //TODO: do something
        }
        if ($customer->getPhone() != $order->getCustomer()->getPhone()) {
          //TODO: do something
        }
        $customer->addOrder($order);
        $order->setCustomer($customer);
      }
    } else {
      $customer = $this->em->getRepository('MeVisaCRMBundle:Customers')->find($order->getCustomer()->getId());
    }
    if (empty($customer)) {
      // New Customer
      $customer = $order->getCustomer();
    }
    $customer->addOrder($order);

    $this->em->persist($customer);
  }

  public function stateEffect($order)
  {
    switch ($order->getState()) {
      case "post":
        $order->setPostedAt(new \DateTime());
        $order->setCompletedAt(null);
        break;
      case "approved":
      case "rejected":
        if ($order->getOrderCompanions()->count() == 1) {
          $companions = $order->getOrderCompanions();
          foreach ($companions as $companion) {
            $companion->setState($order->getState());
          }
        }
      case "cancelled":
        $order->setCompletedAt(new \DateTime());
        break;
      case "deleted":
        $order->setDeletedAt(new \DateTime());
        break;
      default:
        $order->setPostedAt(null);
        $order->setCompletedAt(null);
        $order->setDeletedAt(null);
        break;
    }
    return $order;
  }

  public function setOrderDetails($order)
  {
    /* Auto assign details */

    $order = $this->stateEffect($order);

//        $wcId;
//        $updatedAt;
//        $postedAt;
//        $deletedAt;
//        $completedAt;

    /* Order Customer */
    $this->setCustomer($order);

    /* Order Details */
    // TODO: State machine
//        $productsTotal;
//        $adjustmentTotal;
//        $total;
//        $people;
//        $arrival;
//        $departure;

    /* Order Products */
    $orderProducts = $order->getOrderProducts();
    foreach ($orderProducts as $orderProduct) {
      if (empty($orderProduct->getId())) {
        $productPrice = $orderProduct->getProduct()->getPricing()->last();
        $orderProduct->setUnitCost($productPrice->getCost());
        $order->addOrderProduct($orderProduct);
      }
    }

    /* Order Payments */
    $orderPayments = $order->getOrderPayments();
    foreach ($orderPayments as $payment) {
      if ("paid" == $payment->getState()) {
        $payment->setCreatedAt(new \DateTime());
      }
      if (empty($payment->getId())) {
        $order->addOrderPayment($payment);
      }
    }

    /* Order Companions */
    $orderCompanions = $order->getOrderCompanions();
    // TODO: Check Order Companions
    foreach ($orderCompanions as $companion) {
      $order->addOrderCompanion($companion);
    }

    /* Order Docs */
    $orderDocuments = $order->getOrderDocuments();
    foreach ($orderDocuments as $document) {
      if (empty($document->getId())) {
        $order->addOrderDocument($document);
      }
    }

    /* Order Notes */
    $orderComments = $order->getOrderComments();
    foreach ($orderComments as $comment) {
      if (empty($comment->getId())) {
        if (null == $comment->getComment() || "" == trim($comment->getComment())) {
          $order->removeOrderComment($comment);
          $this->em->remove($comment);
        } else {
          $newComment = new \MeVisa\ERPBundle\Entity\OrderComments();
          $newComment->setCreatedAt(new \DateTime());
          $newComment->setComment($comment->getComment());
          $this->securityContext->getToken()->getUser()->addComment($newComment);
          $order->addOrderComment($newComment);
        }
      } else {
        $this->em->refresh($comment);
      }
    }

    /* Order Invoice */
    $invoices = $order->getInvoices();
    foreach ($invoices as $invoice) {
      if (empty($invoice->getId())) {
        $order->addInvoice($invoice);
      }
    }

    /* Order Receipt */
  }

}
