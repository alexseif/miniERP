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
     * @Route("/", name="pricing")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $request = Request::createFromGlobals();
        $product_id = $request->query->get('product_id');

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('MeVisaERPBundle:Products')->find($product_id);
        //TODO: validate product exists
        $entities = $em->getRepository('MeVisaERPBundle:ProductPrices')->findBy(array('product' => $product_id));

        return array(
            'entities' => $entities,
            'product' => $product
        );
    }

    /**
     * Creates a new ProductPrices entity.
     *
     * @Route("/", name="pricing_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:ProductPrices:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $request = Request::createFromGlobals();
        $product_id = $request->query->get('product_id');
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('MeVisaERPBundle:Products')->find($product_id);

        $entity = new ProductPrices();
        $entity->setCreatedAt(new \DateTime());


        $entity->setProduct($product);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
//            var_dump($entity);
//            die();
            $em->flush();

            return $this->redirect($this->generateUrl('pricing', array('product_id' => $entity->getProduct()->getId())));
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
//var_dump($entity);die();
        $form = $this->createForm(new ProductPricesType(), $entity, array(
            'action' => $this->generateUrl('pricing_create', array('product_id' => $entity->getProduct()->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr' => array('class' => 'btn-success pull-right')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new ProductPrices entity.
     *
     * @Route("/new", name="pricing_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $request = Request::createFromGlobals();
        $product_id = $request->query->get('product_id');
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('MeVisaERPBundle:Products')->find($product_id);

        $entity = new ProductPrices();
        $entity->setProduct($product);
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'product' => $product
        );
    }

    /**
     * Finds and displays a ProductPrices entity.
     *
     * @Route("/{id}", name="pricing_show")
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
     * @Route("/{id}/edit", name="pricing_edit")
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
            'action' => $this->generateUrl('pricing_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Update',
            'attr' => array('class' => 'btn-success pull-right')
        ));

        return $form;
    }

    /**
     * Edits an existing ProductPrices entity.
     *
     * @Route("/{id}", name="pricing_update")
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

            return $this->redirect($this->generateUrl('pricing_edit', array('id' => $id)));
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
     * @Route("/{id}", name="pricing_delete")
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

        return $this->redirect($this->generateUrl('pricing'));
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
                        ->setAction($this->generateUrl('pricing_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
