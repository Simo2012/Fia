<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LivraisonType
 *
 * @ORM\Table(name="LivraisonType")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\LivraisonTypeRepository")
 */
class LivraisonType
{
    const AUCUN = 0;

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
     * @ORM\Column(name="libelle", type="string", length=50)
     */
    private $libelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="affichage", type="boolean", options={"default" = true})
     */
    private $affichage;

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
     * @return LivraisonType
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
     * Set affichage
     *
     * @param boolean $affichage
     *
     * @return LivraisonType
     */
    public function setAffichage($affichage)
    {
        $this->affichage = $affichage;

        return $this;
    }

    /**
     * Get affichage
     *
     * @return boolean
     */
    public function getAffichage()
    {
        return $this->affichage;
    }
}
