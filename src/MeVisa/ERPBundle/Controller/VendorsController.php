<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\Vendors;
use MeVisa\ERPBundle\Form\VendorsType;

/**
 * Vendors controller.
 *
 * @Route("/vendors")
 */
class VendorsController extends Controller
{

    /**
     * Lists all Vendors entities.
     *
     * @Route("/", name="admin_vendors")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:Vendors')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Vendors entity.
     *
     * @Route("/", name="admin_vendors_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:Vendors:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Vendors();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_vendors_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
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
            'action' => $this->generateUrl('admin_vendors_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Vendors entity.
     *
     * @Route("/new", name="admin_vendors_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Vendors();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Vendors entity.
     *
     * @Route("/{id}", name="admin_vendors_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:Vendors')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendors entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Vendors entity.
     *
     * @Route("/{id}/edit", name="admin_vendors_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:Vendors')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendors entity.');
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
    * Creates a form to edit a Vendors entity.
    *
    * @param Vendors $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Vendors $entity)
    {
        $form = $this->createForm(new VendorsType(), $entity, array(
            'action' => $this->generateUrl('admin_vendors_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Vendors entity.
     *
     * @Route("/{id}", name="admin_vendors_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:Vendors:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:Vendors')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendors entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_vendors_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Vendors entity.
     *
     * @Route("/{id}", name="admin_vendors_delete")
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

        return $this->redirect($this->generateUrl('admin_vendors'));
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
            ->setAction($this->generateUrl('admin_vendors_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
