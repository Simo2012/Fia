<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TombolaTirage
 *
 * @ORM\Table(name="TombolaTirage")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TombolaTirageRepository")
 */
class TombolaTirage
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
     * @var \DateTime
     *
     * @ORM\Column(name="tirageDate", type="datetime")
     */
    private $tirageDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDeb", type="datetime")
     */
    private $dateDeb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime")
     */
    private $dateFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="ticketMin", type="integer")
     */
    private $ticketMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="ticketMax", type="integer")
     */
    private $ticketMax;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbParticipations", type="integer")
     */
    private $nbParticipations;
    
    /**
     * @var TombolaTirageState
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TombolaTirageState", inversedBy="tombolaTirage")
     * @ORM\JoinColumn(name="tombolatiragestate_id", referencedColumnName="id", nullable=false)
     */
    private $tombolatiragestate;


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
     * Set tirageDate
     *
     * @param \DateTime $tirageDate
     *
     * @return TombolaTirage
     */
    public function setTirageDate($tirageDate)
    {
        $this->tirageDate = $tirageDate;

        return $this;
    }

    /**
     * Get tirageDate
     *
     * @return \DateTime
     */
    public function getTirageDate()
    {
        return $this->tirageDate;
    }

    /**
     * Set dateDeb
     *
     * @param \DateTime $dateDeb
     *
     * @return TombolaTirage
     */
    public function setDateDeb($dateDeb)
    {
        $this->dateDeb = $dateDeb;

        return $this;
    }

    /**
     * Get dateDeb
     *
     * @return \DateTime
     */
    public function getDateDeb()
    {
        return $this->dateDeb;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return TombolaTirage
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set ticketMin
     *
     * @param integer $ticketMin
     *
     * @return TombolaTirage
     */
    public function setTicketMin($ticketMin)
    {
        $this->ticketMin = $ticketMin;

        return $this;
    }

    /**
     * Get ticketMin
     *
     * @return integer
     */
    public function getTicketMin()
    {
        return $this->ticketMin;
    }

    /**
     * Set ticketMax
     *
     * @param integer $ticketMax
     *
     * @return TombolaTirage
     */
    public function setTicketMax($ticketMax)
    {
        $this->ticketMax = $ticketMax;

        return $this;
    }

    /**
     * Get ticketMax
     *
     * @return integer
     */
    public function getTicketMax()
    {
        return $this->ticketMax;
    }

    /**
     * Set nbParticipations
     *
     * @param integer $nbParticipations
     *
     * @return TombolaTirage
     */
    public function setNbParticipations($nbParticipations)
    {
        $this->nbParticipations = $nbParticipations;

        return $this;
    }

    /**
     * Get nbParticipations
     *
     * @return integer
     */
    public function getNbParticipations()
    {
        return $this->nbParticipations;
    }
    
    /**
     * Set tombolatiragestate
     *
     * @param TombolaTirageState $tombolatiragestate
     *
     * @return TombolaTirageState
     */
    public function setTombolaTirageState(TombolaTirageState $tombolatiragestate)
    {
        $this->tombolatiragestate = $tombolatiragestate;

        return $this;
    }

    /**
     * Get tombolatiragestate
     *
     * @return TombolaTirageState
     */
    public function getTombolaTirageState()
    {
        return $this->tombolatiragestate;
    }
}

