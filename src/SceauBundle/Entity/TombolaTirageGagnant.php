<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TombolaTirageGagnant
 *
 * @ORM\Table(name="TombolaTirageGagnant")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TombolaTirageGagnantRepository")
 */
class TombolaTirageGagnant
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
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;

    /**
     * @var integer
     *
     * @ORM\Column(name="isValide", type="integer")
     */
    private $isValide;
    
    /**
     * @var TombolaGain
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TombolaGain", inversedBy="tombolaTirageGagnant")
     * @ORM\JoinColumn(name="tombolagain_id", referencedColumnName="id", nullable=false)
     */
    private $tombolagain;
    
    /**
     * @var TombolaTicket
     *
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TombolaTicket", inversedBy="tombolaTirageGagnant")
     * @ORM\JoinColumn(name="tombolaticket_id", referencedColumnName="id", nullable=false)
     */
    private $tombolaticket;


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
     * Set rang
     *
     * @param integer $rang
     *
     * @return TombolaTirageGagnant
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set isValide
     *
     * @param integer $isValide
     *
     * @return TombolaTirageGagnant
     */
    public function setIsValide($isValide)
    {
        $this->isValide = $isValide;

        return $this;
    }

    /**
     * Get isValide
     *
     * @return integer
     */
    public function getIsValide()
    {
        return $this->isValide;
    }
    
    /**
     * Set tombolagain
     *
     * @param TombolaGain $tombolagain
     *
     * @return TombolaGain
     */
    public function setTombolaGain(TombolaGain $tombolagain)
    {
        $this->tombolagain = $tombolagain;

        return $this;
    }

    /**
     * Get tombolagain
     *
     * @return TombolaGain
     */
    public function getTombolaGain()
    {
        return $this->tombolagain;
    }
    
    /**
     * Set tombolaticket
     *
     * @param TombolaTicket $tombolaticket
     *
     * @return TombolaTicket
     */
    public function setTombolaTicket(TombolaTicket $tombolaticket)
    {
        $this->tombolaticket = $tombolaticket;

        return $this;
    }

    /**
     * Get tombolaticket
     *
     * @return TombolaTicket
     */
    public function getTombolaTicket()
    {
        return $this->tombolaticket;
    }
}

