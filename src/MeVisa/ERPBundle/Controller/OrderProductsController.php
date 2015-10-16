<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\OrderProducts;
use MeVisa\ERPBundle\Form\OrderProductsType;

/**
 * OrderProducts controller.
 *
 * @Route("/orderproducts")
 */
class OrderProductsController extends Controller
{

    /**
     * Lists all OrderProducts entities.
     *
     * @Route("/", name="orderproducts")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:OrderProducts')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OrderProducts entity.
     *
     * @Route("/", name="orderproducts_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:OrderProducts:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new OrderProducts();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orderproducts_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a OrderProducts entity.
     *
     * @param OrderProducts $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrderProducts $entity)
    {
        $form = $this->createForm(new OrderProductsType(), $entity, array(
            'action' => $this->generateUrl('orderproducts_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new OrderProducts entity.
     *
     * @Route("/new", name="orderproducts_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new OrderProducts();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OrderProducts entity.
     *
     * @Route("/{id}", name="orderproducts_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderProducts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OrderProducts entity.
     *
     * @Route("/{id}/edit", name="orderproducts_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderProducts entity.');
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
    * Creates a form to edit a OrderProducts entity.
    *
    * @param OrderProducts $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(OrderProducts $entity)
    {
        $form = $this->createForm(new OrderProductsType(), $entity, array(
            'action' => $this->generateUrl('orderproducts_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing OrderProducts entity.
     *
     * @Route("/{id}", name="orderproducts_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:OrderProducts:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:OrderProducts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrderProducts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('orderproducts_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OrderProducts entity.
     *
     * @Route("/{id}", name="orderproducts_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:OrderProducts')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OrderProducts entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orderproducts'));
    }

    /**
     * Creates a form to delete a OrderProducts entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orderproducts_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
