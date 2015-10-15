<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class AdminController extends Controller
{
    /**
     * Show admin dashboard.
     *
     * @Route("/", name="sceau_admin")
     */
    public function indexAction() 
    {
        return $this->render("SceauBundle:Admin/Dashboard:index.html.twig");
    }
}
