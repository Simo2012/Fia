<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FluxStatut
 *
 * @ORM\Table(name="FluxStatut")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\FluxStatutRepository")
 */
class FluxStatut
{
    const FLUX_A_TRAITER = 1;
    const FLUX_EN_COURS_DE_TRAITEMENT = 2;
    const FLUX_VALIDE = 3;
    const FLUX_INVALIDE = 4;

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
     * @return FluxStatut
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
