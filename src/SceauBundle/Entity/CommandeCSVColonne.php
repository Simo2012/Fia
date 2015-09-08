<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeCSVColonne
 *
 * @ORM\Table(name="CommandeCSVColonne")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\CommandeCSVColonneRepository")
 */
class CommandeCSVColonne
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
     * @ORM\Column(name="position", type="smallint")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;

    /**
     * @var array
     *
     * @ORM\Column(name="parametrage", type="jsonb_array")
     */
    private $parametrage;


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
     * Set position
     *
     * @param integer $position
     *
     * @return CommandeCSVColonne
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return CommandeCSVColonne
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
     * Set parametrage
     *
     * @param array $parametrage
     *
     * @return CommandeCSVColonne
     */
    public function setParametrage($parametrage)
    {
        $this->parametrage = $parametrage;

        return $this;
    }

    /**
     * Get parametrage
     *
     * @return array
     */
    public function getParametrage()
    {
        return $this->parametrage;
    }
}
