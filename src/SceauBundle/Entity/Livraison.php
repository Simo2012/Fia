<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Livraison
 *
 * @ORM\Table(name="Livraison")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\LivraisonRepository")
 */
class Livraison
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
     * @ORM\Column(name="zone", type="string", length=250)
     */
    private $zone;

    /**
     * @var string
     *
     * @ORM\Column(name="delai", type="string", length=50)
     */
    private $delai;

    /**
     * @var string
     *
     * @ORM\Column(name="coutLivraison", type="string", length=100)
     */
    private $coutLivraison;


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
     * Set zone
     *
     * @param string $zone
     *
     * @return Livraison
     */
    public function setZone($zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone
     *
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set delai
     *
     * @param string $delai
     *
     * @return Livraison
     */
    public function setDelai($delai)
    {
        $this->delai = $delai;

        return $this;
    }

    /**
     * Get delai
     *
     * @return string
     */
    public function getDelai()
    {
        return $this->delai;
    }

    /**
     * Set coutLivraison
     *
     * @param string $coutLivraison
     *
     * @return Livraison
     */
    public function setCoutLivraison($coutLivraison)
    {
        $this->coutLivraison = $coutLivraison;

        return $this;
    }

    /**
     * Get coutLivraison
     *
     * @return string
     */
    public function getCoutLivraison()
    {
        return $this->coutLivraison;
    }
}

