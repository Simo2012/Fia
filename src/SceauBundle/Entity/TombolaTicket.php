<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TombolaTicket
 *
 * @ORM\Table(name="TombolaTicket")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TombolaTicketRepository")
 */
class TombolaTicket
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
     * @ORM\Column(name="dateInsert", type="datetime")
     */
    private $dateInsert;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateParticipe", type="datetime")
     */
    private $dateParticipe;
    
    /**
     * @var Membre
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\Membre", inversedBy="tombolaTicket")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id", nullable=false)
     */
    private $membre;
    
    /**
     * @var TombolaSource
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TombolaSource", inversedBy="tombolaTicket")
     * @ORM\JoinColumn(name="tombolasource_id", referencedColumnName="id", nullable=false)
     */
    private $tombolasource;
    
    /**
    * @var ArrayCollection
    *
    * @ORM\OneToMany(targetEntity="SceauBundle\Entity\TombolaTirageGagnant", mappedBy="tombolaticket")
    */
    private $tombolaTirageGagnant;


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
     * Set dateInsert
     *
     * @param \DateTime $dateInsert
     *
     * @return TombolaTicket
     */
    public function setDateInsert($dateInsert)
    {
        $this->dateInsert = $dateInsert;

        return $this;
    }

    /**
     * Get dateInsert
     *
     * @return \DateTime
     */
    public function getDateInsert()
    {
        return $this->dateInsert;
    }

    /**
     * Set dateParticipe
     *
     * @param \DateTime $dateParticipe
     *
     * @return TombolaTicket
     */
    public function setDateParticipe($dateParticipe)
    {
        $this->dateParticipe = $dateParticipe;

        return $this;
    }

    /**
     * Get dateParticipe
     *
     * @return \DateTime
     */
    public function getDateParticipe()
    {
        return $this->dateParticipe;
    }
    
    /**
     * Set membre
     *
     * @param Membre $membre
     *
     * @return Membre
     */
    public function setMembre(Membre $membre)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre
     *
     * @return Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }
    
    /**
     * Set TombolaSource
     *
     * @param TombolaSource $tombolasource
     *
     * @return TombolaSource
     */
    public function setTombolaSource(TombolaSource $tombolasource)
    {
        $this->tombolasource = $tombolasource;

        return $this;
    }

    /**
     * Get tombolasource
     *
     * @return TombolaSource
     */
    public function getTombolaSource()
    {
        return $this->tombolasource;
    }
    
    /**
     * Get tombolaTirageGagnant
     *
     * @return TombolaTirageGagnant
    */
    function getTombolaTirageGagnant()
    {
        return $this->tombolaTirageGagnant;
    }
    
    /**
     * Set TombolaTirageGagnant
     *
     * @param TombolaSource $tombolaTirageGagnant
     *
     * @return TombolaTirageGagnant
     */
    function setTombolaTirageGagnant($tombolaTirageGagnant)
    {
        $this->tombolaTirageGagnant = $tombolaTirageGagnant;
    }


    
    
}