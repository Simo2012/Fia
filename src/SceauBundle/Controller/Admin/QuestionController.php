<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Admin\TicketReponseType;
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
        $entityRepo = $this->get('sceau.repository.ticket');

        $entity = $entityRepo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ticket entity.');
        }

        $formTicketReponse = $this->createForm(new TicketReponseType());

        return array(
            'entity'            => [],
            'formTicketReponse' => $formTicketReponse->createView(),
        );
    }

    /**
     * yolo
     *
     * @Route("/{id}/updateTicketReponseModele", name="update_ticket_reponse_modele")
     * @Method("POST")
     */
    public function updateTicketReponseModele()
    {
        $request = $this->get('request');

        if($request->isXmlHttpRequest()) {  
            
            $modeleId = $request->request->get('modeleType');

            if ($modeleId) {
                $entityRepo = $this->get('sceau.repository.ticket.reponse.modele');
                $ticketReponseModele = $entityRepo->find($modeleId);

                if (!$ticketReponseModele) {
                    return new Response('Unable to find TicketReponseModele entity', 404);
                }

                $reponse = [
                    'sujet'     => $ticketReponseModele->getSujet(),
                    'message'   => $ticketReponseModele->getMessage(),
                ];
                return new Response(json_encode($reponse)); 
            }                  
        }
    }
}
