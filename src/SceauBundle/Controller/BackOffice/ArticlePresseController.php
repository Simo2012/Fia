<?php

namespace SceauBundle\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Pagerfanta\Pagerfanta;

class ArticlePresseController extends Controller
{
    /**
     * List all published Articles.
     *
     * @Route("/articles/{page}", name="articles")
     * @Method("GET")
     */
    public function indexAction(Request $request, $page)
    {
        $articlePresseRepo = $this->get('sceau.repository.article.presse');
        $adapter = $articlePresseRepo->getAllArticlePresse();
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->getParameter('pagination_size'))
        ->setCurrentPage($page)
        ;
        var_dump("ragou");die();
        //return $this->render('SceauBundle:BackOffice:index.html.twig', array('tabArticlePresse' => $pagerfanta));
    }
}