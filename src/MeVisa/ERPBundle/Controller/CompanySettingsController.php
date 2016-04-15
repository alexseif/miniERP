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

        return array(
            'entity' => $entity,
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

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
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

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('companysettings_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

}
