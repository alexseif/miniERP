<?php

namespace Admin\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Admin\AdminBundle\Form\UsersType;

/**
 * Users controller.
 *
 * @Route("/users")
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

        $users = $em->getRepository('AdminAdminBundle:User')->findAll();

        return array(
            'users' => $users,
        );
    }

    /**
     * @Route("/{id}", name="users_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminAdminBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Customers entity.');
        }
        return array(
            'user' => $user,
        );
    }

    /**
     * @Route("/new", name="users_new")
     * @Template()
     */
    public function newAction()
    {
        dump($this->getParameter('security.role_hierarchy.roles'));

        //TODO: http://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
        return array(
                // ...
        );
    }

    /**
     * @Route("/{id}/edit", name="users_edit")
     * @Template()
     */
    public function editAction($id)
    {

        $rolesArray = $this->getParameter('security.role_hierarchy.roles');
        $roles = array();
        foreach ($rolesArray as $role => $value) {
            $roles[$role]=trim(str_replace('ROLE', '', str_replace('_', ' ', $role)));
//            $roles[] = $role;
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminAdminBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find Customers entity.');
        }

        $user_form = $this->createForm(new UsersType($roles), $user, array(
            'action' => $this->generateUrl('customers_update', array('id' => $user->getId())),
            'method' => 'PUT',
        ));
        $user_form->add('submit', 'submit', array(
            'label' => 'Update',
            'attr' => array(
                'class' => 'btn-success pull-right'
            )
        ));

        //TODO: http://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
        return array(
            'user' => $user,
            'user_form' => $user_form->createView()
        );
    }

}
