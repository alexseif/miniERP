<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\OrderPayments;
use MeVisa\ERPBundle\Form\OrderPaymentsType;

/**
 * OrderPayments controller.
 *
 * @Route("/orderpayments")
 */
class OrderPaymentsController extends Controller
{

    /**
     * Lists all OrderPayments entities.
     *
     * @Route("/", name="orderpayments")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:OrderPayments')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OrderPayments entity.
     *
     * @Route("/", name="orderpayments_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:OrderPayments:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new OrderPayments();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orderpayments_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a OrderPayments entity.
     *
     * @param OrderPayments $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrderPayments $entity)
    {
        $form = $this->createForm(new OrderPaymentsType(), $entity, array(
            'action' => $this->generateUrl('orderpayments_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new OrderPayments entity.
     *
     * @Route("/new", name="orderpayments_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new OrderPayments();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OrderPayments entity.
     *
     * @Route("/{id}", name="orderpayments_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderPayments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderPayments entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OrderPayments entity.
     *
     * @Route("/{id}/edit", name="orderpayments_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderPayments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderPayments entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a OrderPayments entity.
    *
    * @param OrderPayments $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(OrderPayments $entity)
    {
        $form = $this->createForm(new OrderPaymentsType(), $entity, array(
            'action' => $this->generateUrl('orderpayments_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing OrderPayments entity.
     *
     * @Route("/{id}", name="orderpayments_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:OrderPayments:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderPayments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderPayments entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('orderpayments_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OrderPayments entity.
     *
     * @Route("/{id}", name="orderpayments_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:OrderPayments')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OrderPayments entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orderpayments'));
    }

    /**
     * Creates a form to delete a OrderPayments entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orderpayments_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
