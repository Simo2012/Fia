<?php

namespace SceauBundle\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ArticlePresseController extends Controller
{
    /**
     * List all published Articles.
     *
     * @Route("/articles", name="articles")
     * @Method("GET")
     */
    public function indexAction(Request $request, $page)
    {
        $articlePresseRepo = $this->get('sceau.repository.article.presse');
        $articlePresses = $articlePresseRepo->findBy(array(), array('date' => 'ASC'));

        return $this->render('SceauBundle:Admin/Articles:index.html.twig', array('articlePresses' => $articlePresses));

    }
}