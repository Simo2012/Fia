<?php

namespace SceauBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CommandeCSVParametrage
 *
 * @ORM\Table(name="CommandeCSVParametrage")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\CommandeCSVParametrageRepository")
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
     * @ORM\Column(name="dossierStockage", type="string", length=255)
     */
    private $dossierStockage;

    /**
     * @var string
     *
     * @ORM\Column(name="separateur", type="string", length=5)
     */
    private $separateur;

    /**
     * @var array
     *
     * @ORM\Column(name="correspondances", type="jsonb_array", nullable=true)
     */
    private $correspondances;

    /**
     * @var array
     *
     * @ORM\Column(name="unicite", type="jsonb_array", nullable=true)
     */
    private $unicite;

    /**
     * @var bool
     *
     * @ORM\Column(name="sousSiteActif", type="boolean", options={"default"=false})
     */
    private $sousSiteActif;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SceauBundle\Entity\CommandeCSVColonne")
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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCommandeCSVColonnes()
    {
        return $this->commandeCSVColonnes;
    }

    /**
     * Set correspondances
     *
     * @param array $correspondances
     *
     * @return CommandeCSVParametrage
     */
    public function setCorrespondances($correspondances)
    {
        $this->correspondances = $correspondances;

        return $this;
    }

    /**
     * Get correspondances
     *
     * @return array
     */
    public function getCorrespondances()
    {
        return $this->correspondances;
    }

    /**
     * Set dossierStockage
     *
     * @param string $dossierStockage
     *
     * @return CommandeCSVParametrage
     */
    public function setDossierStockage($dossierStockage)
    {
        $this->dossierStockage = $dossierStockage;

        return $this;
    }

    /**
     * Get dossierStockage
     *
     * @return string
     */
    public function getDossierStockage()
    {
        return $this->dossierStockage;
    }

    /**
     * Set unicite
     *
     * @param array $unicite
     *
     * @return CommandeCSVParametrage
     */
    public function setUnicite($unicite)
    {
        $this->unicite = $unicite;

        return $this;
    }

    /**
     * Get unicite
     *
     * @return array
     */
    public function getUnicite()
    {
        return $this->unicite;
    }

    /**
     * Set sousSiteActif
     *
     * @param boolean $sousSiteActif
     *
     * @return CommandeCSVParametrage
     */
    public function setSousSiteActif($sousSiteActif)
    {
        $this->sousSiteActif = $sousSiteActif;

        return $this;
    }

    /**
     * Get sousSiteActif
     *
     * @return boolean
     */
    public function getSousSiteActif()
    {
        return $this->sousSiteActif;
    }
}
