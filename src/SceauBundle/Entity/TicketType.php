<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketType
 *
 * @ORM\Table(name="TicketType")
 * @ORM\Entity
 */
class TicketType
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
     * @ORM\Column(name="libelle", type="string", length=200)
     */
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity="SceauBundle\Entity\TicketActeur")
     * @ORM\JoinColumn(name="acteur_id", referencedColumnName="id", nullable=true)
     */
    private $acteur;

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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return TicketType
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set acteur
     *
     * @param \SceauBundle\Entity\TicketActeur $acteur
     *
     * @return TicketType
     */
    public function setActeur(\SceauBundle\Entity\TicketActeur $acteur = null)
    {
        $this->acteur = $acteur;

        return $this;
    }

    /**
     * Get acteur
     *
     * @return \SceauBundle\Entity\TicketActeur
     */
    public function getActeur()
    {
        return $this->acteur;
    }
}
