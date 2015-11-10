<?php

namespace MeVisa\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="dashboard")
     * @Template()
     */
    public function dashboardAction()
    {
        $em = $this->getDoctrine()->getManager();

        $backoffice = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("backoffice");
        $document = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("document");
        $post = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("post");
        $completed = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("approved");

        return array(
            "backoffice" => $backoffice,
            "document" => $document,
            "post" => $post,
            "completed" => $completed,
        );
    }

}
