<?php

namespace Admin\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Users controller.
 *
 * @Route("/users")
 */
class UsersController extends Controller
{

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AdminAdminBundle:User')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/show")
     * @Template()
     */
    public function showAction()
    {
        return array(
                // ...
        );
    }

    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction()
    {
        //TODO: http://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
        return array(
                // ...
        );
    }

}
