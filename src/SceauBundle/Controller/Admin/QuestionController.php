<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * QuestionController controller.
 *
 * @Route("/questions")
 */
class QuestionController extends Controller
{

    /**
     * Questions.
     *
     * @Route("/", name="questions")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        // $entityRepo = $this->get('sceau.repository.question');
        // $entities = $entityRepo->findBy(array(), array('date' => 'ASC'));

        return array(
            'entities' => [1,2,3,4]
        );
    }


    /**
     * Finds and displays a InternauteQuestion entity.
     *
     * @Route("/{id}", name="question_show")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function showAction($id)
    {
        // $articlePresseRepo = $this->get('sceau.repository.article.presse');
        // $entity = $articlePresseRepo->find($id);

        // if (!$entity) {
        //     throw $this->createNotFoundException('Unable to find ArticlePresse entity.');
        // }

        // $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => []
        );
    }
}
