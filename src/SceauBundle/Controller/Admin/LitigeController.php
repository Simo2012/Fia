<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * LitigeController controller.
 *
 * @Route("/litiges", name="litiges")
 */
class LitigeController extends Controller
{

    /**
     * Litiges.
     *
     * @Route("/attente-intervention", name="litiges_intervention")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Litiges/AttenteIntervention:index.html.twig")
     */
    public function indexAction(Request $request)
    {


    }

    /**
     * List all published Articles.
     *
     * @Route("/en-cours-usurpation", name="litiges_usurpation")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Litiges/EnCoursUsurpation:index.html.twig")
     */
    public function usurpationAction(Request $request)
    {

    }

}
