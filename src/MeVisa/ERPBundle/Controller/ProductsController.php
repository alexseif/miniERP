<?php

namespace MeVisa\ERPBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
   * Lists all Products products.
   *
   * @Route("/", name="products")
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    $encoders = array(new XmlEncoder(), new JsonEncoder());
    $normalizers = array(new ObjectNormalizer());
    $serializer = new Serializer($normalizers, $encoders);

    $em = $this->getDoctrine()->getManager();

    $products = $em->getRepository('MeVisaERPBundle:Products')->findAll();
    foreach ($products as $product) {
      if (!is_array($product->getRequiredDocuments())) {
        $product->setRequiredDocuments($serializer->decode($product->getRequiredDocuments(), 'json'));
      }
    }

    return array(
      'products' => $products,
    );
  }

  /**
   * Creates a form to create a Products product.
   *
   * @param Products $product The product
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createCreateForm(Products $product)
  {
    $form = $this->createForm(new ProductsType(), $product, array(
      'action' => $this->generateUrl('products_new'),
      'method' => 'POST',
    ));

    $form->add('submit', 'submit', array(
      'label' => 'Save',
      'attr' => array('class' => 'btn-success pull-right')
    ));

    return $form;
  }

  /**
   * Displays a form to create a new Products product.
   *
   * @Security("has_role('ROLE_ACCOUNTANT')")
   * @Route("/new", name="products_new")
   * @Method({"GET", "POST"})
   * @Template()
   */
  public function newAction(Request $request)
  {
    $product = new Products();
    $product->setEnabled(true);
    $form = $this->createCreateForm($product);

    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if ($form->isValid()) {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $product->setRequiredDocuments($serializer->serialize($product->getRequiredDocuments(), 'json'));

        $pricing = $product->getPricing();
        foreach ($pricing as $price) {
          if (empty($price->getId())) {
            $product->addPricing($price);
          }
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->redirect($this->generateUrl('products_show', array('id' => $product->getId())));
      }
    }

    return array(
      'product' => $product,
      'product_form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a Products product.
   *
   * @Route("/{id}", name="products_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $product = $em->getRepository('MeVisaERPBundle:Products')->find($id);

    if (!$product) {
      throw $this->createNotFoundException('Unable to find Products product.');
    }
    if (!is_array($product->getRequiredDocuments())) {
      $encoders = array(new XmlEncoder(), new JsonEncoder());
      $normalizers = array(new ObjectNormalizer());
      $serializer = new Serializer($normalizers, $encoders);
      $product->setRequiredDocuments($serializer->decode($product->getRequiredDocuments(), 'json'));
    }


    return array(
      'product' => $product,
    );
  }

  /**
   * Displays a form to edit an existing Products product.
   *
   * @Security("has_role('ROLE_ACCOUNTANT')")
   * @Route("/{id}/edit", name="products_edit")
   * @Method({"GET", "PUT"})
   * @Template()
   */
  public function editAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $product = $em->getRepository('MeVisaERPBundle:Products')->find($id);

    if (!$product) {
      throw $this->createNotFoundException('Unable to find Products product.');
    }

    if (!is_array($product->getRequiredDocuments())) {
      $encoders = array(new XmlEncoder(), new JsonEncoder());
      $normalizers = array(new ObjectNormalizer());
      $serializer = new Serializer($normalizers, $encoders);
      $product->setRequiredDocuments($serializer->decode($product->getRequiredDocuments(), 'json'));
    }

    $editForm = $this->createEditForm($product);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted()) {
      if ($editForm->isValid()) {
        $productPrices = $product->getPricing();
        foreach ($productPrices as $price) {
          if (empty($price->getId())) {
            $product->addPricing($price);
          } else {
            $em->refresh($price);
          }
        }
        $em->flush();
        return $this->redirect($this->generateUrl('products_show', array('id' => $id)));
      }
    }

    return array(
      'product' => $product,
      'product_form' => $editForm->createView(),
    );
  }

  /**
   * Creates a form to edit a Products product.
   *
   * @param Products $product The product
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createEditForm(Products $product)
  {
    $form = $this->createForm(new ProductsType(), $product, array(
      'action' => $this->generateUrl('products_edit', array('id' => $product->getId())),
      'method' => 'PUT',
    ));

    $form->add('submit', 'submit', array(
      'label' => 'Save',
      'attr' => array('class' => 'btn-success pull-right')
    ));

    return $form;
  }

}
