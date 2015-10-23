<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketReponseModele
 *
 * @ORM\Table(name="ticketreponsemodele")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TicketReponseModeleRepository")
 */
class TicketReponseModele
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
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="sujet", type="string", length=100)
     */    
    private $sujet;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string")
     */    
    private $message;



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
     * Set type
     *
     * @param string $type
     *
     * @return TicketReponseModele
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set sujet
     *
     * @param string $sujet
     *
     * @return TicketReponseModele
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet
     *
     * @return string
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return TicketReponseModele
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \longtext
     */
    public function getMessage()
    {
        return $this->message;
    }
}
