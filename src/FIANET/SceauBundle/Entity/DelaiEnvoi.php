<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DelaiEnvoi
 *
 * @ORM\Table(name="DelaiEnvoi")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\DelaiEnvoiRepository")
 */
class DelaiEnvoi
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbJours", type="smallint")
     */
    private $nbJours;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50)
     */
    private $libelle;


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
     * Set nbJours
     *
     * @param integer $nbJours
     * @return DelaiEnvoi
     */
    public function setNbJours($nbJours)
    {
        $this->nbJours = $nbJours;

        return $this;
    }

    /**
     * Get nbJours
     *
     * @return integer
     */
    public function getNbJours()
    {
        return $this->nbJours;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return DelaiEnvoi
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
}
