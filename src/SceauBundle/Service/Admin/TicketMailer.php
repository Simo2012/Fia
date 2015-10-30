<?php

namespace SceauBundle\Service\Admin;

use Doctrine\ORM\EntityManager;

class TicketMailer
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createTicketReponseEmail(Ticket $ticket, $emailData)
    {
        $envoiEmail = new EnvoiEmail();

        $envoiEmail->setSubject($emailData['sujet']);
        $envoiEmail->setSendFrom($emailData['sujet']);
        $envoiEmail->setSendTo($ticket->getAuteur()->getEmail());
        $envoiEmail->setDateInsert(new \Datetime());
        $envoiEmail->setContent($emailData['message']);

        $this->em->persist($envoiEmail);
        $this->em->flush();
    }
}
