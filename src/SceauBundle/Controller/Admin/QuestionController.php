<?php

namespace SceauBundle\Controller\Admin;

use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Form\Type\Admin\TicketNoteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Admin\TicketReponseType;
use SceauBundle\Form\Type\Admin\Filters\TicketFiltersType;

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

        $params = $request->query->all();

        $tickets = $this->get('sceau.repository.ticket')->getTicketsByParams($params);
        // $form = $this->get('form.factory')->create(new TicketFiltersType($params), null);
        
        // $form->handleRequest($request);
        // if ($form->isValid()) {
        //     $params = $form->getData();

        //     return $this->redirect($this->generateUrl('vw_prm_admin_stats_index', $params));
        // }



        // $repo = $this->getDoctrine()->getManager()->getRepository('SceauBundle\Entity\Ticket');
        // $questions = $questionRepository->findBy(array(), array('date' => 'ASC'));
        $ticketFilters = $this->createForm(new TicketFiltersType());

        return array(
            'entities'      => $tickets,
            'ticketFilters' => $ticketFilters->createView(),
        );
    }


    /**
     * Finds and displays a Ticket entity.
     *
     * @Route("/{id}", name="question_show")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function showAction(Ticket $ticket)
    {
        $historiques = $this->get('sceau.repository.ticket.historique')->findByTicket($ticket);

        $ticketNoteForm = $this->createForm(new TicketNoteType(), $ticket, array(
            'action' => $this->generateUrl('question_update',array('id'=>$ticket->getId())),
            'method' => 'POST',
        ));
        $formTicketReponse = $this->createForm(new TicketReponseType());
        
        return array(
            'ticket'            => $ticket,
            'formTicketReponse' => $formTicketReponse->createView(),
            'ticketNoteForm'    => $ticketNoteForm->createView(),
            'historiques'       => $historiques,
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
     *  Update a ticket's note
     *
     * @Route("/add/{id}", name="question_update")
     * @Method("POST")
     */
    public function updateNoteAction(Request $request, Ticket $ticket)
    {
        $ticketNoteForm = $this->createForm(new TicketNoteType(), $ticket);

        $ticketNoteForm->handleRequest($request);

        if ($ticketNoteForm->isValid()) {
            $note = $ticketNoteForm->get('note')->getData();
            $ticket->setNote($note);
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

        }
        return $this->redirect($this->generateUrl('question_show', array('id' => $ticket->getId())));
    }

    /**
     * Delete a ticket's note
     *
     * @Route("/{id}/deleteNote", name="question_note_delete")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function deleteNoteAction(Ticket $ticket)
    {
        $ticket->setNote(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);
        $em->flush();

        return $this->redirect($this->generateUrl('question_show', array('id' => $ticket->getId())));

    }
}
