<?php

namespace FIANET\SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeCSVParametrage
 *
 * @ORM\Table(name="CommandeCSVParametrage")
 * @ORM\Entity(repositoryClass="FIANET\SceauBundle\Entity\CommandeCSVParametrageRepository")
 */
class CommandeCSVParametrage
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
     * @var string
     *
     * @ORM\Column(name="separateur", type="string", length=5)
     */
    private $separateur;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbJoursUnicite", type="smallint", nullable=true)
     */
    private $nbJoursUnicite;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FIANET\SceauBundle\Entity\CommandeCSVColonne")
     * @ORM\JoinTable(name="CommandeCSVParametrage_CommandeCSVColonne",
     *      joinColumns={@ORM\JoinColumn(name="commandeCSVParametrage_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="commandeCSVColonne_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $commandeCSVColonnes;


    public function __construct()
    {
        $this->commandeCSVColonnes = new ArrayCollection();
    }


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
     * Set separateur
     *
     * @param string $separateur
     *
     * @return CommandeCSVParametrage
     */
    public function setSeparateur($separateur)
    {
        $this->separateur = $separateur;

        return $this;
    }

    /**
     * Get separateur
     *
     * @return string
     */
    public function getSeparateur()
    {
        return $this->separateur;
    }

    /**
     * Set nbJoursUnicite
     *
     * @param integer $nbJoursUnicite
     *
     * @return CommandeCSVParametrage
     */
    public function setNbJoursUnicite($nbJoursUnicite)
    {
        $this->nbJoursUnicite = $nbJoursUnicite;

        return $this;
    }

    /**
     * Get nbJoursUnicite
     *
     * @return integer
     */
    public function getNbJoursUnicite()
    {
        return $this->nbJoursUnicite;
    }

    /**
     * Add commandeCSVColonne
     *
     * @param CommandeCSVColonne $commandeCSVColonne
     *
     * @return CommandeCSVParametrage
     */
    public function addCommandeCSVColonne(CommandeCSVColonne $commandeCSVColonne)
    {
        $this->commandeCSVColonnes[] = $commandeCSVColonne;

        return $this;
    }

    /**
     * Remove commandeCSVColonne
     *
     * @param CommandeCSVColonne $commandeCSVColonne
     */
    public function removeCommandeCSVColonne(CommandeCSVColonne $commandeCSVColonne)
    {
        $this->commandeCSVColonnes->removeElement($commandeCSVColonne);
    }

    /**
     * Get commandeCSVColonnes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommandeCSVColonnes()
    {
        return $this->commandeCSVColonnes;
    }
}
