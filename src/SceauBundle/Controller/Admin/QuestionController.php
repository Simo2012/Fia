<?php

namespace SceauBundle\Controller\Admin;

use SceauBundle\Entity\Ticket;
use SceauBundle\Form\Type\Admin\TicketNoteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

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
     * Finds and displays a Ticket entity.
     *
     * @Route("/{id}", name="question_show")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function showAction(Ticket $ticket, $id)
    {
        $ticketNoteForm = $this->createForm(new TicketNoteType(), $ticket, array(
            'action' => $this->generateUrl('question_update',array('id'=>$id)),
            'method' => 'POST',
        ));

        return array(
            'ticketNoteForm' => $ticketNoteForm->createView(),
            'ticket'      => $ticket,
        );
    }

    /**
     *  Update a ticket's note
     *
     * @Route("/add/{id}", name="question_update")
     * @Method("POST")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function updateAction(Request $request, Ticket $ticket, $id)
    {
        $ticketNoteForm = $this->createForm(new TicketNoteType(), $ticket);

        $ticketNoteForm->handleRequest($request);

        if ($ticketNoteForm->isValid()) {
            $note = $ticketNoteForm->get('note')->getData();
            $ticket->setNote($note);
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

        }
        return $this->redirect($this->generateUrl('question_show', array('id' => $id)));
    }

    /**
     * Delete a ticket's note
     *
     * @Route("/{id}/deleteNote", name="question_note_delete")
     * @Method("GET")
     * @Template("SceauBundle:Admin/Questions:show.html.twig")
     */
    public function deleteNoteAction(Ticket $ticket, $id)
    {
        $ticket->setNote(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);
        $em->flush();

        return $this->redirect($this->generateUrl('question_show', array('id' => $id)));

    }
}
