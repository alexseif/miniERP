<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\AgreementTemplate;
use MeVisa\ERPBundle\Form\AgreementTemplateType;

/**
 * AgreementTemplate controller.
 *
 * @Route("/agreement")
 */
class AgreementTemplateController extends Controller
{

    /**
     * Lists all AgreementTemplate entities.
     *
     * @Route("/", name="agreement")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:AgreementTemplate')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new AgreementTemplate entity.
     *
     * @Route("/", name="agreement_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:AgreementTemplate:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new AgreementTemplate();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('agreement_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a AgreementTemplate entity.
     *
     * @param AgreementTemplate $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AgreementTemplate $entity)
    {
        $form = $this->createForm(new AgreementTemplateType(), $entity, array(
            'action' => $this->generateUrl('agreement_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AgreementTemplate entity.
     *
     * @Route("/new", name="agreement_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AgreementTemplate();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AgreementTemplate entity.
     *
     * @Route("/{id}", name="agreement_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:AgreementTemplate')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AgreementTemplate entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AgreementTemplate entity.
     *
     * @Route("/{id}/edit", name="agreement_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:AgreementTemplate')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AgreementTemplate entity.');
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
    * Creates a form to edit a AgreementTemplate entity.
    *
    * @param AgreementTemplate $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AgreementTemplate $entity)
    {
        $form = $this->createForm(new AgreementTemplateType(), $entity, array(
            'action' => $this->generateUrl('agreement_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AgreementTemplate entity.
     *
     * @Route("/{id}", name="agreement_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:AgreementTemplate:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:AgreementTemplate')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AgreementTemplate entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('agreement_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a AgreementTemplate entity.
     *
     * @Route("/{id}", name="agreement_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:AgreementTemplate')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AgreementTemplate entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('agreement'));
    }

    /**
     * Creates a form to delete a AgreementTemplate entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('agreement_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
