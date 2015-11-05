<?php

namespace SceauBundle\Controller\Admin;

use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Entity\EnvoiEmail;
use SceauBundle\Entity\TicketHistoriqueEmail;
use SceauBundle\Form\Type\Admin\TicketHistoriqueEmailType;
use SceauBundle\Form\Type\Admin\TicketNoteType;
use SceauBundle\Form\Type\Admin\TicketReafectationType;
use SceauBundle\Form\Type\Admin\TicketHistoriqueEmailDisabledType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Admin\Filters\TicketFiltersType;
use Symfony\Component\Validator\Constraints\Date;

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
        $filtersForm = $this->get('form.factory')->create(new TicketFiltersType($params), null);
        $filtersForm->handleRequest($request);
        
        if ($filtersForm->isValid()) {
            $params = $filtersForm->getData();
            return $this->redirect($this->generateUrl('questions', $params));
        }

        $tickets = $this->get('sceau.repository.ticket')->getTicketsByParams($params);
 
        return array(
            'entities'      => $tickets,
            'filtersForm'   => $filtersForm->createView(),
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
        $historiques = $this->get('sceau.repository.ticket.historique')->findByTicket($ticket, array('date' => 'ASC'));

        $ticketNoteForm = $this->createForm(new TicketNoteType(), $ticket, array(
            'action' => $this->generateUrl('question_update',array('id'=>$ticket->getId())),
            'method' => 'POST',
        ));

        $ticketHistoriqueEmail = new TicketHistoriqueEmail();
        $ticketHistoriqueEmailForm = $this->createForm(new TicketHistoriqueEmailType(), $ticketHistoriqueEmail, array(
            'action' => $this->generateUrl('question_reponse',array('id'=>$ticket->getId())),
            'method' => 'POST',
        ));

        $ticketReafectationForm = $this->createForm(new TicketReafectationType(), $ticket, array(
            'action' => $this->generateUrl('question_reafectation',array('id'=>$ticket->getId())),
            'method' => 'POST',
        ));

        return array(
            'ticket'                    => $ticket,
            'ticketHistoriqueEmailForm' => $ticketHistoriqueEmailForm->createView(),
            'ticketNoteForm'            => $ticketNoteForm->createView(),
            'ticketReafectationForm'    => $ticketReafectationForm->createView(),
            'historiques'               => $historiques,
        );
    }

    /**
     *  Reponse à un ticket
     *
     * @Route("/{id}/reponse", name="question_reponse")
     * @Method("POST")
     */
    public function ticketReponseAction(Request $request, Ticket $ticket)
    {
        $ticketHistoriqueEmail = new TicketHistoriqueEmail();
        $ticketHistoriqueEmailForm = $this->createForm(new TicketHistoriqueEmailType(), $ticketHistoriqueEmail);

        $ticketHistoriqueEmailForm->handleRequest($request);

        if ($ticketHistoriqueEmailForm->isValid()) {
            $ticketHistoriqueEmail = $ticketHistoriqueEmailForm->getData();
            $em = $this->getDoctrine()->getManager();
            $ticketHistoriqueEmail->setMailTo($ticket->getAuteur()->getEmail());
            $em->persist($ticketHistoriqueEmail);
            $em->flush();

            // new historique for email reponse
            $this->get('sceau.admin.ticket_historique_service')->createTicketEmailHistorique($ticket, $ticketHistoriqueEmail);
        }
        return $this->redirect($this->generateUrl('question_show', array('id' => $ticket->getId())));
    }

    /**
     *  Reafecter un ticket
     *
     * @Route("/{id}/reafecter", name="question_reafectation")
     * @Method("POST")
     */
    public function ticketReafectationAction(Request $request, Ticket $ticket)
    {
        $ticketReafectationForm = $this->createForm(new TicketReafectationType(), $ticket);
  
        $ticketReafectationForm->handleRequest($request);

        if ($ticketReafectationForm->isValid()) {
            $ticket = $ticketReafectationForm->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('question_show', array('id' => $ticket->getId())));
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
            $ticket = $ticketNoteForm->getData();

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


    /**
     * update ticket response with model
     *
     * @Route("/{id}/reponse-modele", name="question_reponse_modele")
     * @Method("POST")
     */
    public function updateTicketReponseModele(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $modeleId = $request->request->get('modeleType');

            if ($modeleId) {
                $entityRepo = $this->get('sceau.repository.ticket.reponse.modele');
                $ticketReponseModele = $entityRepo->find($modeleId);

                if (!$ticketReponseModele) {
                    return new Response('Unable to find TicketReponseModele entity', 404);
                }

                $reponse = [
                    'sujet' => $ticketReponseModele->getSujet(),
                    'message' => $ticketReponseModele->getMessage(),
                ];
                return new Response(json_encode($reponse));
            }
        }
    }

    /**
     * Get a HistoriqueEmail by id
     *
     * @Route("/historiqueEmail/{id}", name="question_historique_email")
     *
     * @return Response
     */

    public function getHistoriqueEmailAction($id)
    {
        /** @var \SceauBundle\Entity\Repository\TicketHistoriqueRepository $historiqueRepository */
        $ticketHistoriqueRepository = $this->get('sceau.repository.ticket.historique');
        $ticketHistorique = $ticketHistoriqueRepository->find($id);
        $ticketHistoriqueEmail = $ticketHistorique->getTicketHistoriqueEmail();

        $ticketHistoriqueEmailDisabledForm = $this->createForm(new TicketHistoriqueEmailDisabledType(), $ticketHistoriqueEmail);

        return $this->render('SceauBundle:Admin/Questions:historique_email_content.html.twig', array(
            'ticket'                            => $ticketHistorique->getTicket(),
            'ticketHistoriqueEmailDisabledForm' => $ticketHistoriqueEmailDisabledForm->createView(),
           
        ));
    }
}
