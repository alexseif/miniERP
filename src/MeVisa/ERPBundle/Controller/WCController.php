<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MeVisa\ERPBundle\Entity\WC;
use MeVisa\ERPBundle\Form\WCType;

/**
 * WC controller.
 *
 * @Route("/wc")
 */
class WCController extends Controller
{

    /**
     * Lists all WC entities.
     *
     * @Route("/", name="wc")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MeVisaERPBundle:WC')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new WC entity.
     *
     * @Route("/", name="wc_create")
     * @Method("POST")
     * @Template("MeVisaERPBundle:WC:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new WC();
        $entity->setPost($request->__toString());

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

// return $this->redirect($this->generateUrl('wc_show', array('id' => $entity->getId())));

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Creates a form to create a WC entity.
     *
     * @param WC $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(WC $entity)
    {
        $form = $this->createForm(new WCType(), $entity, array(
            'action' => $this->generateUrl('wc_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new WC entity.
     *
     * @Route("/new", name="wc_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $request = Request::createFromGlobals();
        $entity = new WC();
        $entity->setGet($request->__toString());

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        $form = $this->createCreateForm($entity);

        return array(
            'request' => $request->__toString(),
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a WC entity.
     *
     * @Route("/{id}", name="wc_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:WC')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WC entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing WC entity.
     *
     * @Route("/{id}/edit", name="wc_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:WC')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WC entity.');
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
     * Creates a form to edit a WC entity.
     *
     * @param WC $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(WC $entity)
    {
        $form = $this->createForm(new WCType(), $entity, array(
            'action' => $this->generateUrl('wc_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing WC entity.
     *
     * @Route("/{id}", name="wc_update")
     * @Method("PUT")
     * @Template("MeVisaERPBundle:WC:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MeVisaERPBundle:WC')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WC entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('wc_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a WC entity.
     *
     * @Route("/{id}", name="wc_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MeVisaERPBundle:WC')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find WC entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('wc'));
    }

    /**
     * Creates a form to delete a WC entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('wc_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
