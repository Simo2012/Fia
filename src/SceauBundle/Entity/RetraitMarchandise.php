<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RetraitMarchandise
 *
 * @ORM\Table(name="RetraitMarchandise")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\RetraitMarchandiseRepository")
 */
class RetraitMarchandise
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
     * @ORM\Column(name="pointRetrait", type="string", length=500)
     */
    private $pointRetrait;

    /**
     * @var string
     *
     * @ORM\Column(name="boutique", type="string", length=500)
     */
    private $boutique;

    /**
     * @var string
     *
     * @ORM\Column(name="bureauPoste", type="string", length=500)
     */
    private $bureauPoste;


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
     * Set pointRetrait
     *
     * @param string $pointRetrait
     *
     * @return RetraitMarchandise
     */
    public function setPointRetrait($pointRetrait)
    {
        $this->pointRetrait = $pointRetrait;

        return $this;
    }

    /**
     * Get pointRetrait
     *
     * @return string
     */
    public function getPointRetrait()
    {
        return $this->pointRetrait;
    }

    /**
     * Set boutique
     *
     * @param string $boutique
     *
     * @return RetraitMarchandise
     */
    public function setBoutique($boutique)
    {
        $this->boutique = $boutique;

        return $this;
    }

    /**
     * Get boutique
     *
     * @return string
     */
    public function getBoutique()
    {
        return $this->boutique;
    }

    /**
     * Set bureauPoste
     *
     * @param string $bureauPoste
     *
     * @return RetraitMarchandise
     */
    public function setBureauPoste($bureauPoste)
    {
        $this->bureauPoste = $bureauPoste;

        return $this;
    }

    /**
     * Get bureauPoste
     *
     * @return string
     */
    public function getBureauPoste()
    {
        return $this->bureauPoste;
    }
}

