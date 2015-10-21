<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CoordonneesClient
 *
 * @ORM\Table(name="CoordonneesClient")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\CoordonneesClientRepository")
 */
class CoordonneesClient
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
     * @ORM\Column(name="adresse", type="string", length=250)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="numRcs", type="string", length=50)
     */
    private $numRcs;


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
     * Set adresse
     *
     * @param string $adresse
     *
     * @return CoordonneesClient
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set numRcs
     *
     * @param string $numRcs
     *
     * @return CoordonneesClient
     */
    public function setNumRcs($numRcs)
    {
        $this->numRcs = $numRcs;

        return $this;
    }

    /**
     * Get numRcs
     *
     * @return string
     */
    public function getNumRcs()
    {
        return $this->numRcs;
    }
}

