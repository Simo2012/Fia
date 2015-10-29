<?php

namespace SceauBundle\Controller\Admin;

use SceauBundle\Entity\Ticket;
use SceauBundle\Entity\TicketHistorique;
use SceauBundle\Entity\EnvoiEmail;
use SceauBundle\Entity\TicketHistoriqueEmail;
use SceauBundle\Form\Type\Admin\TicketHistoriqueEmailType;
use SceauBundle\Form\Type\Admin\TicketNoteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use SceauBundle\Form\Type\Admin\TicketReponseType;
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
        $historiques = $this->get('sceau.repository.ticket.historique')->findByTicket($ticket);

        $ticketNoteForm = $this->createForm(new TicketNoteType(), $ticket, array(
            'action' => $this->generateUrl('question_update',array('id'=>$ticket->getId())),
            'method' => 'POST',
        ));

        $ticketReponseForm = $this->createForm(new TicketReponseType(), null, array(
            'action' => $this->generateUrl('question_reponse',array('id'=>$ticket->getId())),
            'method' => 'POST',
        ));
        
        return array(
            'ticket'            => $ticket,
            'ticketReponseForm' => $ticketReponseForm->createView(),
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
     *  Reponse à un ticket
     *
     * @Route("/{id}/reponse", name="question_reponse")
     * @Method("POST")
     */
    public function ticketReponseAction(Request $request, Ticket $ticket)
    {
        var_dump('ticketReponseAction');
        $ticketReponseForm = $this->createForm(new TicketReponseType());

        $ticketReponseForm->handleRequest($request);

        if ($ticketReponseForm->isValid()) {
            $data = $ticketReponseForm->getData();
            // $envoiMail = new EnvoiEmail();
            // $envoiMail->setSubjet($data['sujet']);
            // $envoiMail->setSendFrom($data['expediteur']);
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
        $historiqueRepository = $this->get('sceau.repository.ticket.historique');

        $historique = $historiqueRepository->find($id);
        $historiqueEmailForm = $this->createForm(new TicketHistoriqueEmailType, $historique->getHistoriqueEmail());

        return $this->render('SceauBundle:Admin/Questions:historique_email_content.html.twig', array(
            'historiqueEmailForm' => $historiqueEmailForm->createView(),
            'ticket'              => $historique->getTicket(),
        ));

    }
}
