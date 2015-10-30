<?php
namespace SceauBundle\Listener\Entity;

final class TicketEvents
{
    const TICKET_REPONSE 		= 'ticket.reponse';
    const TICKET_STATE_CHANGE  	= 'ticket.state.change';
    const TICKET_NOTE_CREATE    = 'ticket.note.create'; //note creation action;
    const TICKET_NOTE_UPDATE  	= 'ticket.note.update';	//note update action;
    const TICKET_NOTE_DELETE  	= 'ticket.note.delete'; //note delete action;
    const TICKET_REAFECTATION_CATEGORIE		= 'ticket.reafectation.categorie';
    const TICKET_REAFECTATION_DESTINATAIRE	= 'ticket.reafectation.destinataire';
}