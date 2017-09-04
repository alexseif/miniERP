<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MeVisa\ERPBundle\Entity\Vendors;
use MeVisa\ERPBundle\Form\VendorsType;

/**
 * Vendors controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/vendors")
 */
class VendorsController extends Controller
{

  /**
   * Lists all Vendors entities.
   *
   * @Route("/", name="vendors")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $vendors = $em->getRepository('MeVisaERPBundle:Vendors')->findAll();

    return array(
      'vendors' => $vendors,
    );
  }

  /**
   * Creates a new Vendors entity.
   *
   * @Security("has_role('ROLE_SUPER_ADMIN')")
   * @Route("/", name="vendors_create")
   * @Method("POST")
   * @Template("MeVisaERPBundle:Vendors:new.html.twig")
   */
  public function createAction(Request $request)
  {
    $vendor = new Vendors();
    $form = $this->createCreateForm($vendor);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($vendor);
      $em->flush();

      return $this->redirect($this->generateUrl('vendors_show', array('id' => $vendor->getId())));
    }

    return array(
      'vendor' => $vendor,
      'vendor_form' => $form->createView(),
    );
  }

  /**
   * Creates a form to create a Vendors entity.
   *
   * @param Vendors $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createCreateForm(Vendors $entity)
  {
    $form = $this->createForm(new VendorsType(), $entity, array(
      'action' => $this->generateUrl('vendors_create'),
      'method' => 'POST',
    ));

    $form->add('submit', 'submit', array(
      'label' => 'Save',
      'attr' => array(
        'class' => 'btn-success pull-right',
      )
    ));

    return $form;
  }

  /**
   * Displays a form to create a new Vendors entity.
   *
   * @Security("has_role('ROLE_SUPER_ADMIN')")
   * @Route("/new", name="vendors_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction()
  {
    $vendor = new Vendors();
    $form = $this->createCreateForm($vendor);

    return array(
      'vendor' => $vendor,
      'vendor_form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a Vendors entity.
   *
   * @Route("/{id}", name="vendors_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $vendor = $em->getRepository('MeVisaERPBundle:Vendors')->find($id);

    if (!$vendor) {
      throw $this->createNotFoundException('Unable to find Vendors entity.');
    }

    return array(
      'vendor' => $vendor,
      'vendor_delete' => $this->createDeleteForm($id)->createView()
    );
  }

  /**
   * Displays a form to edit an existing Vendors entity.
   *
   * @Security("has_role('ROLE_SUPER_ADMIN')")
   * @Route("/{id}/edit", name="vendors_edit")
   * @Method("GET")
   * @Template()
   */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $vendor = $em->getRepository('MeVisaERPBundle:Vendors')->find($id);

    if (!$vendor) {
      throw $this->createNotFoundException('Unable to find Vendors entity.');
    }

    $editForm = $this->createEditForm($vendor);

    return array(
      'vendor' => $vendor,
      'vendor_form' => $editForm->createView(),
    );
  }

  /**
   * Creates a form to edit a Vendors entity.
   *
   * @param Vendors $entity The entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createEditForm(Vendors $entity)
  {
    $form = $this->createForm(new VendorsType(), $entity, array(
      'action' => $this->generateUrl('vendors_update', array('id' => $entity->getId())),
      'method' => 'PUT',
    ));

    $form->add('submit', 'submit', array('label' => 'Update',
      'attr' => array(
        'class' => 'btn-circle btn-success',
      )
    ));

    return $form;
  }

  /**
   * Edits an existing Vendors entity.
   *
   * @Security("has_role('ROLE_SUPER_ADMIN')")
   * @Route("/{id}", name="vendors_update")
   * @Method("PUT")
   * @Template("MeVisaERPBundle:Vendors:edit.html.twig")
   */
  public function updateAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();

    $vendor = $em->getRepository('MeVisaERPBundle:Vendors')->find($id);

    if (!$vendor) {
      throw $this->createNotFoundException('Unable to find Vendors entity.');
    }

    $editForm = $this->createEditForm($vendor);
    $editForm->handleRequest($request);

    if ($editForm->isValid()) {
      $em->flush();

      return $this->redirect($this->generateUrl('vendors_edit', array('id' => $id)));
    }

    return array(
      'vendor' => $vendor,
      'vendor_form' => $editForm->createView(),
    );
  }

  /**
   * Deletes a Vendors entity.
   *
   * @Security("has_role('ROLE_SUPER_ADMIN')")
   * @Route("/{id}", name="vendors_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id)
  {
    $form = $this->createDeleteForm($id);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('MeVisaERPBundle:Vendors')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Vendors entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('vendors'));
  }

  /**
   * Creates a form to delete a Vendors entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('vendors_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
    ;
  }

}
