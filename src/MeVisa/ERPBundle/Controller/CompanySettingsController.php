<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\CompanySettings;
use MeVisa\ERPBundle\Form\CompanySettingsType;

/**
 * CompanySettings controller.
 *
 * @Route("/companysettings")
 */
class CompanySettingsController extends Controller
{

    /**
     * Lists all CompanySettings entities.
     *
     * @Route("/", name="companysettings")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:CompanySettings')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new CompanySettings entity.
     *
     * @Route("/", name="companysettings_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:CompanySettings:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CompanySettings();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('companysettings_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a CompanySettings entity.
     *
     * @param CompanySettings $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CompanySettings $entity)
    {
        $form = $this->createForm(new CompanySettingsType(), $entity, array(
            'action' => $this->generateUrl('companysettings_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CompanySettings entity.
     *
     * @Route("/new", name="companysettings_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CompanySettings();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a CompanySettings entity.
     *
     * @Route("/preview", name="companysettings_preview")
     * @Method("GET")
     * @Template()
     */
    public function previewAction()
    {
        $em = $this->getDoctrine()->getManager();

        $CompanySettings = $em->getRepository('MeVisaERPBundle:CompanySettings')->find(1);

        if (!$CompanySettings) {
            throw $this->createNotFoundException('Unable to find CompanySettings entity.');
        }

        $order = new \MeVisa\ERPBundle\Entity\Orders();
        $customer= new \MeVisa\CRMBundle\Entity\Customers();
        $customer->setName("Preview");
        $order->setCustomer($customer);
        $product = new \MeVisa\ERPBundle\Entity\Products();
        $product->setName("Preivew");
        $orderProduct = New \MeVisa\ERPBundle\Entity\OrderProducts();
        $orderProduct->setProduct($product);
        $order->addOrderProduct($orderProduct);
        $invoice = new \MeVisa\ERPBundle\Entity\Invoices();

        return array(
            'companySettings' => $CompanySettings,
            'order' => $order,
            'invoice' => $invoice,
        );
    }

    /**
     * Finds and displays a CompanySettings entity.
     *
     * @Route("/{id}", name="companysettings_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:CompanySettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanySettings entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CompanySettings entity.
     *
     * @Route("/{id}/edit", name="companysettings_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:CompanySettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanySettings entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a CompanySettings entity.
     *
     * @param CompanySettings $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CompanySettings $entity)
    {
        $form = $this->createForm(new CompanySettingsType(), $entity, array(
            'action' => $this->generateUrl('companysettings_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing CompanySettings entity.
     *
     * @Route("/{id}", name="companysettings_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:CompanySettings:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:CompanySettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanySettings entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('companysettings_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a CompanySettings entity.
     *
     * @Route("/{id}", name="companysettings_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:CompanySettings')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CompanySettings entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('companysettings'));
    }

    /**
     * Creates a form to delete a CompanySettings entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('companysettings_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}