<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Currency;
use AppBundle\Form\CurrencyType;

/**
 * Currency controller.
 *
 * @Route("/admin/currency")
 */
class CurrencyController extends Controller
{

    /**
     * Lists all Currency entities.
     *
     * @Route("/", name="admin_currency")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        // Form creation
        $entity = new Currency();
        $form = $this->createCreateForm($entity);

        //List details
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Currency')->findBy(array(), array('createdAt' => 'Desc'), 7);

        return array(
            'entity' => $entity,
            'create_form' => $form->createView(),
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Currency entity.
     *
     * @Route("/", name="admin_currency_create")
     * @Method("POST")
     * 
     */
    public function createAction(Request $request)
    {
        $entity = new Currency();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $entity->setCur1('USD');
        $entity->setCur2('RUB');
        $entity->setCreatedAt(new \DateTime());

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Success: 1 USD is now ' . number_format($entity->getValue(), 2) . ' RUB');

            return $this->redirect($this->generateUrl('admin_currency'));
        }
        return $this->redirect($this->generateUrl('admin_currency'));

//        return array(
//            'entity' => $entity,
//            'form' => $form->createView(),
//        );
    }

    /**
     * Creates a form to create a Currency entity.
     *
     * @param Currency $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Currency $entity)
    {
        $form = $this->createForm(new CurrencyType(), $entity, array(
            'action' => $this->generateUrl('admin_currency_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Set', 'attr' => array('class' => 'btn-success pull-right')));

        return $form;
    }

}
