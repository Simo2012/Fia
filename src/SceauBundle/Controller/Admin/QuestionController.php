<?php

namespace SceauBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Admin\TicketReponseType;
use SceauBundle\Form\Type\Admin\Filters\TicketFiltersType;
use SceauBundle\Entity\Ticket;

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
        $entityRepo = $this->get('sceau.repository.ticket');
        $entities = $entityRepo->findAll(array(), array('date' => 'ASC'));
        $ticketFilters = $this->createForm(new TicketFiltersType());

        return array(
            'entities'      => $entities,
            'ticketFilters' => $ticketFilters->createView(),
        );
    }


    /**
     * Finds and displays a InternauteQuestion entity.
     *
     * @Route("/{id}", name="question_show")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function showAction(Ticket $ticket)
    {
        $formTicketReponse = $this->createForm(new TicketReponseType());
        return array(
            'ticket'            => $ticket,
            'formTicketReponse' => $formTicketReponse->createView(),
        );
    }

    /**
     * update ticket response with model
     *
     * @Route("/{id}/reponse-modele", name="question_reponse_modele")
     * @Method("POST")
     */
    public function updateTicketReponseModele(Request $request)
    {
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

    /**
     * update tickets data from filters
     *
     * @Route("/update-tickets", name="question_update_filters")
     * @Method("POST")
     */
    public function updateTicketsIndex(Request $request)
    {
        if($request->isXmlHttpRequest()) {  
            $filtersForm = $request->request->all();
            foreach ($filtersForm as $key => $value) {
                if ($value == '') {
                    unset($filtersForm[$key]);
                }
            }
            // var_dump('ta mere en slip');
            // $em = $this->getDoctrine()->getEntityManager();
            //$entityRepo = $em->getRepository('SceauBundle:Ticket');

            $entityRepo = $this->get('sceau.repository.ticket');
            $entities = $entityRepo->findBy(['id' => 11]);
            
            return $this->render("SceauBundle:Admin/Questions:list.html.twig", array(
                'entities'      => $entities,
            ));
            // if ($modeleId) {
            //     $entityRepo = $this->get('sceau.repository.ticket.reponse.modele');
            //     $ticketReponseModele = $entityRepo->find($modeleId);

            //     if (!$ticketReponseModele) {
            //         return new Response('Unable to find TicketReponseModele entity', 404);
            //     }

            //     $reponse = [
            //         'sujet'     => $ticketReponseModele->getSujet(),
            //         'message'   => $ticketReponseModele->getMessage(),
            //     ];
            //     return new Response(json_encode($reponse)); 
            // }                  
        }
    }
}
