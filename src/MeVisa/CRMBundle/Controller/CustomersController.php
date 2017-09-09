<?php

namespace MeVisa\CRMBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\CRMBundle\Entity\Customers;
use MeVisa\CRMBundle\Form\CustomersType;

/**
 * Customers controller.
 *
 * @Route("/customers")
 */
class CustomersController extends Controller
{

  /**
   * Lists all Customers entities.
   *
   * @Route("/", name="customers")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $customers = $em->getRepository('MeVisaCRMBundle:Customers')->findAll();

    return array(
      'customers' => $customers,
    );
  }

  /**
   * Creates a new Customers entity.
   *
   * @Route("/", name="customers_create")
   * @Method("POST")
   * @Template("MeVisaCRMBundle:Customers:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $customer = new Customers();
    $form = $this->createCreateForm($customer);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();

        $customerExists = $em->getRepository('MeVisaCRMBundle:Customers')->findOneBy(array("email" => $customer->getEmail()));
        if ($customerExists) {
          $this->addFlash('error_raw', 'Customer email exist: <a class="btn btn-danger" href="' . $this->generateUrl('customers_show', array('id' => $customerExists->getId())) . '">' . $customerExists->getName() . '</a>');
        } else {
          $validator = $this->get('validator');
          $errors = $validator->validate($customer);

          if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return new Response($errorsString);
          }

          $em->persist($customer);
          $em->flush();

          return $this->redirect($this->generateUrl('customers_show', array('id' => $customer->getId())));
        }
      }
    }

    return array(
      'customer' => $customer,
      'customer_form' => $form->createView(),
    );
  }

  /**
   * Creates a form to create a Customers entity.
   *
   * @param Customers $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createCreateForm(Customers $entity)
  {
    $form = $this->createForm(new CustomersType(), $entity, array(
      'action' => $this->generateUrl('customers_create'),
      'method' => 'POST',
    ));
    if ($this->isGranted('ROLE_SUPER_ADMIN')) {
      $form->add('agent', 'checkbox', array(
        'required' => false,
      ));
    }
    $form->add('submit', 'submit', array(
      'label' => 'Create',
      'attr' => array(
        'class' => 'btn-success pull-right'
      )
    ));

    return $form;
  }

  /**
   * Displays a form to create a new Customers entity.
   *
   * @Route("/new", name="customers_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $customer = new Customers();
    $form = $this->createCreateForm($customer);

    return array(
      'customer' => $customer,
      'customer_form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a Customers entity.
   *
   * @Route("/{id}", name="customers_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $customer = $em->getRepository('MeVisaCRMBundle:Customers')->find($id);

    if (!$customer) {
      throw $this->createNotFoundException('Unable to find Customers entity.');
    }


    return array(
      'customer' => $customer,
      'logs' => $this->getCustomerLog($id)
    );
  }

  /**
   * Displays a form to edit an existing Customers entity.
   *
   * @Route("/{id}/edit", name="customers_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $customer = $em->getRepository('MeVisaCRMBundle:Customers')->find($id);

    if (!$customer) {
      throw $this->createNotFoundException('Unable to find Customers entity.');
    }

    $editForm = $this->createEditForm($customer);

    return array(
      'customer' => $customer,
      'customer_form' => $editForm->createView(),
    );
  }

  /**
   * Creates a form to edit a Customers entity.
   *
   * @param Customers $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createEditForm(Customers $entity)
  {
    $form = $this->createForm(new CustomersType(), $entity, array(
      'action' => $this->generateUrl('customers_update', array('id' => $entity->getId())),
      'method' => 'PUT',
    ));
    if ($this->isGranted('ROLE_SUPER_ADMIN')) {
      $form->add('agent', 'checkbox', array(
        'required' => false,
      ));
    }
    $form->add('submit', 'submit', array(
      'label' => 'Update',
      'attr' => array(
        'class' => 'btn-success pull-right'
      )
    ));

    return $form;
  }

  /**
   * Edits an existing Customers entity.
   *
   * @Route("/{id}", name="customers_update")
   * @Method("PUT")
   * @Template("MeVisaCRMBundle:Customers:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();

    $customer = $em->getRepository('MeVisaCRMBundle:Customers')->find($id);

    if (!$customer) {
      throw $this->createNotFoundException('Unable to find Customers entity.');
    }

    $editForm = $this->createEditForm($customer);
    $editForm->handleRequest($request);

    if ($editForm->isValid()) {
      $em->flush();
      return $this->redirect($this->generateUrl('customers_show', array('id' => $id)));
    }

    return array(
      'customer' => $customer,
      'customer_form' => $editForm->createView(),
    );
  }

  /**
   * Deletes a Customers entity.
   *
   * @Route("/{id}", name="customers_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('MeVisaCRMBundle:Customers')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Customers entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('customers'));
  }

  /**
   * Creates a form to delete a Customers entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('customers_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
    ;
  }

  public function getCustomerLog($id)
  {
    $em = $this->getDoctrine()->getManager();

    $logRepo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
    $customerLog = $em->find('MeVisa\CRMBundle\Entity\Customers', $id);
    return $logRepo->getLogEntries($customerLog);
  }

}
