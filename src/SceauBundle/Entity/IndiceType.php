<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndiceType
 *
 * @ORM\Table(name="IndiceType")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\IndiceTypeRepository")
 */
class IndiceType
{
    const MOYENNE = 1;
    const POURCENTAGE = 2;
    const NB_AVIS_PERIODE = 3;
    const NB_AVIS_TOTAL = 4;

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
     * @return IndiceType
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
