<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MoyenPaiment
 *
 * @ORM\Table(name="MoyenPaiment")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\MoyenPaimentRepository")
 */
class MoyenPaiment
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
     * @ORM\Column(name="carteBanquaire", type="string", length=100)
     */
    private $carteBanquaire;

    /**
     * @var string
     *
     * @ORM\Column(name="solutionPaimement", type="string", length=250)
     */
    private $solutionPaimement;

    /**
     * @var string
     *
     * @ORM\Column(name="autres", type="string", length=250)
     */
    private $autres;


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
     * Set carteBanquaire
     *
     * @param string $carteBanquaire
     *
     * @return MoyenPaiment
     */
    public function setCarteBanquaire($carteBanquaire)
    {
        $this->carteBanquaire = $carteBanquaire;

        return $this;
    }

    /**
     * Get carteBanquaire
     *
     * @return string
     */
    public function getCarteBanquaire()
    {
        return $this->carteBanquaire;
    }

    /**
     * Set solutionPaimement
     *
     * @param string $solutionPaimement
     *
     * @return MoyenPaiment
     */
    public function setSolutionPaimement($solutionPaimement)
    {
        $this->solutionPaimement = $solutionPaimement;

        return $this;
    }

    /**
     * Get solutionPaimement
     *
     * @return string
     */
    public function getSolutionPaimement()
    {
        return $this->solutionPaimement;
    }

    /**
     * Set autres
     *
     * @param string $autres
     *
     * @return MoyenPaiment
     */
    public function setAutres($autres)
    {
        $this->autres = $autres;

        return $this;
    }

    /**
     * Get autres
     *
     * @return string
     */
    public function getAutres()
    {
        return $this->autres;
    }
}

