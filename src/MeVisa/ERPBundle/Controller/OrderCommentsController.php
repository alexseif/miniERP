<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\OrderComments;
use MeVisa\ERPBundle\Form\OrderCommentsType;

/**
 * OrderComments controller.
 *
 * @Route("/ordercomments")
 */
class OrderCommentsController extends Controller
{

    /**
     * Lists all OrderComments entities.
     *
     * @Route("/", name="ordercomments")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:OrderComments')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OrderComments entity.
     *
     * @Route("/", name="ordercomments_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:OrderComments:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new OrderComments();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ordercomments_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a OrderComments entity.
     *
     * @param OrderComments $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrderComments $entity)
    {
        $form = $this->createForm(new OrderCommentsType(), $entity, array(
            'action' => $this->generateUrl('ordercomments_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new OrderComments entity.
     *
     * @Route("/new", name="ordercomments_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new OrderComments();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OrderComments entity.
     *
     * @Route("/{id}", name="ordercomments_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderComments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderComments entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OrderComments entity.
     *
     * @Route("/{id}/edit", name="ordercomments_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderComments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderComments entity.');
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
    * Creates a form to edit a OrderComments entity.
    *
    * @param OrderComments $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(OrderComments $entity)
    {
        $form = $this->createForm(new OrderCommentsType(), $entity, array(
            'action' => $this->generateUrl('ordercomments_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing OrderComments entity.
     *
     * @Route("/{id}", name="ordercomments_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:OrderComments:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderComments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderComments entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ordercomments_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OrderComments entity.
     *
     * @Route("/{id}", name="ordercomments_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:OrderComments')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OrderComments entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ordercomments'));
    }

    /**
     * Creates a form to delete a OrderComments entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordercomments_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
