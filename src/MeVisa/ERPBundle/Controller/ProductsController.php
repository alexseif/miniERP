<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\Products;
use MeVisa\ERPBundle\Form\ProductsType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Products controller.
 *
 * @Route("/products")
 */
class ProductsController extends Controller
{

    /**
     * Lists all Products entities.
     *
     * @Route("/", name="admin_products")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:Products')->findAll();
        foreach ($entities as $entity) {
            if (!is_array($entity->getRequiredDocuments())) {
                $entity->setRequiredDocuments($serializer->decode($entity->getRequiredDocuments(), 'json'));
            }
        }

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Products entity.
     *
     * @Route("/", name="admin_products_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:Products:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Products();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $entity->setRequiredDocuments($serializer->serialize($entity->getRequiredDocuments(), 'json'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_products_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Products entity.
     *
     * @param Products $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Products $entity)
    {
        $form = $this->createForm(new ProductsType(), $entity, array(
            'action' => $this->generateUrl('admin_products_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Products entity.
     *
     * @Route("/new", name="admin_products_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Products();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Products entity.
     *
     * @Route("/{id}", name="admin_products_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:Products')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Products entity.');
        }
        if (!is_array($entity->getRequiredDocuments())) {
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $entity->setRequiredDocuments($serializer->decode($entity->getRequiredDocuments(), 'json'));
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Products entity.
     *
     * @Route("/{id}/edit", name="admin_products_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:Products')->find($id);

        if (!is_array($entity->getRequiredDocuments())) {
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $entity->setRequiredDocuments($serializer->decode($entity->getRequiredDocuments(), 'json'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Products entity.');
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
     * Creates a form to edit a Products entity.
     *
     * @param Products $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Products $entity)
    {
        $form = $this->createForm(new ProductsType(), $entity, array(
            'action' => $this->generateUrl('admin_products_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Products entity.
     *
     * @Route("/{id}", name="admin_products_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:Products:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:Products')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Products entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        if (!is_array($entity->getRequiredDocuments())) {
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $entity->setRequiredDocuments($serializer->decode($entity->getRequiredDocuments(), 'json'));
        }

        $editForm = $this->createEditForm($entity);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_products_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Products entity.
     *
     * @Route("/{id}", name="admin_products_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:Products')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Products entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_products'));
    }

    /**
     * Creates a form to delete a Products entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('admin_products_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
