<?php

namespace Admin\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="dashboard")
     * @Template()
     */
    public function dashboardAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pending = $em->getRepository('MeVisaERPBundle:Orders')->findAllPending("pending");
        $backoffice = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("backoffice");
        $document = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("document");
        $post = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("post");
        $completed = $em->getRepository('MeVisaERPBundle:Orders')->findAllByState("approved");

        return array(
            "pending" => $pending,
            "backoffice" => $backoffice,
            "document" => $document,
            "post" => $post,
            "completed" => $completed,
        );
    }

    /**
     * @Route("/search", name="search")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository('MeVisaERPBundle:Orders')->searchQuery($request->get('search'));

        return array(
            "results" => $results
        );
    }

}
