<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\ProductPrices;
use MeVisa\ERPBundle\Form\ProductPricesType;

/**
 * ProductPrices controller.
 *
 * @Route("/pricing")
 */
class ProductPricesController extends Controller
{

    /**
     * Lists all ProductPrices entities.
     *
     * @Route("/", name="admin_pricing")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:ProductPrices')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new ProductPrices entity.
     *
     * @Route("/", name="admin_pricing_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:ProductPrices:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProductPrices();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_pricing_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ProductPrices entity.
     *
     * @param ProductPrices $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductPrices $entity)
    {
        $form = $this->createForm(new ProductPricesType(), $entity, array(
            'action' => $this->generateUrl('admin_pricing_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProductPrices entity.
     *
     * @Route("/new", name="admin_pricing_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProductPrices();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProductPrices entity.
     *
     * @Route("/{id}", name="admin_pricing_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:ProductPrices')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductPrices entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProductPrices entity.
     *
     * @Route("/{id}/edit", name="admin_pricing_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:ProductPrices')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductPrices entity.');
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
     * Creates a form to edit a ProductPrices entity.
     *
     * @param ProductPrices $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProductPrices $entity)
    {
        $form = $this->createForm(new ProductPricesType(), $entity, array(
            'action' => $this->generateUrl('admin_pricing_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ProductPrices entity.
     *
     * @Route("/{id}", name="admin_pricing_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:ProductPrices:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:ProductPrices')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductPrices entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_pricing_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ProductPrices entity.
     *
     * @Route("/{id}", name="admin_pricing_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:ProductPrices')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductPrices entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_pricing'));
    }

    /**
     * Creates a form to delete a ProductPrices entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('admin_pricing_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
