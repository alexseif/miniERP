<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;
use AppBundle\Form\UsersType;
use AppBundle\Form\UsersPasswordType;

/**
 * Users controller.
 *
 * @Route("/users")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class UsersController extends Controller
{

  /**
   * @Route("/", name="users")
   * @Template()
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $users = $em->getRepository('AppBundle:User')->findAll();

    return array(
      'users' => $users,
    );
  }

  /**
   * @Route("/new", name="users_new")
   * @Method({"GET", "PUT"})
   * @Template()
   */
  public function newAction(Request $request)
  {
    $user = new User();
    $user_form = $this->createUserNewForm($user);
    $user_form->handleRequest($request);

    if ($user_form->isSubmitted()) {
      if ($user_form->isValid()) {
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();
        return $this->redirect($this->generateUrl('users_show', array('id' => $user->getId())));
      }
    }

    return array(
      'user' => $user,
      'user_form' => $user_form->createView()
    );
  }

  /**
   * @Route("/{id}", name="users_show")
   * @Template()
   */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository('AppBundle:User')->find($id);
    if (!$user) {
      throw $this->createNotFoundException('Unable to find User entity.');
    }
    return array(
      'user' => $user,
    );
  }

  /**
   * @Route("/{id}/edit", name="users_edit")
   * @Method({"GET", "PUT"})
   * @Template()
   */
  public function editAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository('AppBundle:User')->find($id);
    if (!$user) {
      throw $this->createNotFoundException('Unable to find User entity.');
    }
    $user_form = $this->createUserEditForm($user);
    //TODO: http://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html

    $user_form->handleRequest($request);

    if ($user_form->isSubmitted()) {
      if ($user_form->isValid()) {
        $userManager = $this->container->get('fos_user.user_manager');

        if ($user == $this->getUser()) {
          $this->get('session')->set('_locale', $user->getLocale());
        }

        $em->flush();
        return $this->redirect($this->generateUrl('users_show', array('id' => $user->getId())));
      }
    }


    return array(
      'user' => $user,
      'user_form' => $user_form->createView()
    );
  }

  /**
   * @Route("/{id}/pwd", name="users_pwd")
   * @Method({"GET", "PUT"})
   * @Template()
   */
  public function passwordAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository('AppBundle:User')->find($id);
    if (!$user) {
      throw $this->createNotFoundException('Unable to find User entity.');
    }
    $user_form = $this->createUserPasswordForm($user);
    //TODO: http://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html

    $user_form->handleRequest($request);

    if ($user_form->isSubmitted()) {
      if ($user_form->isValid()) {
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updatePassword($user);

        if ($user == $this->getUser()) {
          $this->get('session')->set('_locale', $user->getLocale());
        }

        $em->flush();
        return $this->redirect($this->generateUrl('users_show', array('id' => $user->getId())));
      }
    }


    return array(
      'user' => $user,
      'user_form' => $user_form->createView()
    );
  }

  private function createUserNewForm($user)
  {
    $rolesArray = $this->getParameter('security.role_hierarchy.roles');
    $roles = array();
    foreach ($rolesArray as $role => $value) {
      $roles[$role] = trim(str_replace('ROLE', '', str_replace('_', ' ', $role)));
    }
    $user_form = $this->createForm(new UsersType($roles), $user, array(
      'action' => $this->generateUrl('users_new'),
      'method' => 'PUT',
    ));
    $user_form->add('submit', 'submit', array(
      'label' => 'Update',
      'attr' => array(
        'class' => 'btn-success pull-right'
      )
    ));
    return $user_form;
  }

  private function createUserEditForm($user)
  {
    $rolesArray = $this->getParameter('security.role_hierarchy.roles');
    $roles = array();
    foreach ($rolesArray as $role => $value) {
      $roles[$role] = trim(str_replace('ROLE', '', str_replace('_', ' ', $role)));
    }
    $user_form = $this->createForm(new UsersType($roles), $user, array(
      'action' => $this->generateUrl('users_edit', array('id' => $user->getId())),
      'method' => 'PUT',
    ));
    $user_form->add('submit', 'submit', array(
      'label' => 'Update',
      'attr' => array(
        'class' => 'btn-success pull-right'
      )
    ));
    return $user_form;
  }

  private function createUserPasswordForm($user)
  {
    $user_form = $this->createForm(new UsersPasswordType(), $user, array(
      'action' => $this->generateUrl('users_pwd', array('id' => $user->getId())),
      'method' => 'PUT',
    ));
    $user_form->add('submit', 'submit', array(
      'label' => 'Update',
      'attr' => array(
        'class' => 'btn-success pull-right'
      )
    ));
    return $user_form;
  }

}
