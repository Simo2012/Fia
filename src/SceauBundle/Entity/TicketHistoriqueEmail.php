<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketHistoriqueEmail
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TicketHistoriqueEmailRepository")
 */
class TicketHistoriqueEmail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mailFrom", type="string", length=255)
     */
    private $mailFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="mailTo", type="string", length=255)
     */
    private $mailTo;

    /**
     * @var string
     *
     * @ORM\Column(name="mailSubject", type="string", length=255)
     */
    private $mailSubject;

    /**
     * @var string
     *
     * @ORM\Column(name="mailBody", type="text", nullable=true)
     */
    private $mailBody;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;
    
    /**
     * @var TicketReponseModele
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TicketReponseModele")
     * @ORM\JoinColumn(name="ticket_reponse_modele_id", referencedColumnName="id", nullable=true)
     */
    private $reponseModele;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mailFrom
     *
     * @param string $mailFrom
     *
     * @return TicketHistoriqueEmail
     */
    public function setMailFrom($mailFrom)
    {
        $this->mailFrom = $mailFrom;

        return $this;
    }

    /**
     * Get mailFrom
     *
     * @return string
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * Set mailTo
     *
     * @param string $mailTo
     *
     * @return TicketHistoriqueEmail
     */
    public function setMailTo($mailTo)
    {
        $this->mailTo = $mailTo;

        return $this;
    }

    /**
     * Get mailTo
     *
     * @return string
     */
    public function getMailTo()
    {
        return $this->mailTo;
    }

    /**
     * Set mailSubject
     *
     * @param string $mailSubject
     *
     * @return TicketHistoriqueEmail
     */
    public function setMailSubject($mailSubject)
    {
        $this->mailSubject = $mailSubject;

        return $this;
    }

    /**
     * Get mailSubject
     *
     * @return string
     */
    public function getMailSubject()
    {
        return $this->mailSubject;
    }

    /**
     * Set mailBody
     *
     * @param string
     *
     * @return TicketHistoriqueEmail
     */
    public function setMailBody($mailBody)
    {
        $this->mailBody = $mailBody;
        return $this;
    }

    /**
     * Get mailBody
     *
     * @return string
     */
    public function getMailBody()
    {
        return $this->mailBody;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TicketHistoriqueEmail
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set reponseModele
     *
     * @param \SceauBundle\Entity\TicketReponseModele $reponseModele
     *
     * @return TicketHistoriqueEmail
     */
    public function setReponseModele(\SceauBundle\Entity\TicketReponseModele $reponseModele = null)
    {
        $this->reponseModele = $reponseModele;

        return $this;
    }

    /**
     * Get reponseModele
     *
     * @return \SceauBundle\Entity\TicketReponseModele
     */
    public function getReponseModele()
    {
        return $this->reponseModele;
    }
}
