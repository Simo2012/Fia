<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TombolaGain
 *
 * @ORM\Table(name="TombolaGain")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TombolaGainRepository")
 */
class TombolaGain
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
     * @var integer
     *
     * @ORM\Column(name="rangMin", type="integer")
     */
    private $rangMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="randMax", type="integer")
     */
    private $randMax;

    /**
     * @var string
     *
     * @ORM\Column(name="gainLib", type="string", length=250)
     */
    private $gainLib;

    /**
     * @var integer
     *
     * @ORM\Column(name="gainValue", type="integer")
     */
    private $gainValue;
    
    /**
    * @var ArrayCollection
    *
    * @ORM\OneToMany(targetEntity="SceauBundle\Entity\TombolaTirageGagnant", mappedBy="tombolagain")
    */
    private $tombolaTirageGagnant;


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
     * Set rangMin
     *
     * @param integer $rangMin
     *
     * @return TombolaGain
     */
    public function setRangMin($rangMin)
    {
        $this->rangMin = $rangMin;

        return $this;
    }

    /**
     * Get rangMin
     *
     * @return integer
     */
    public function getRangMin()
    {
        return $this->rangMin;
    }

    /**
     * Set randMax
     *
     * @param integer $randMax
     *
     * @return TombolaGain
     */
    public function setRandMax($randMax)
    {
        $this->randMax = $randMax;

        return $this;
    }

    /**
     * Get randMax
     *
     * @return integer
     */
    public function getRandMax()
    {
        return $this->randMax;
    }

    /**
     * Set gainLib
     *
     * @param string $gainLib
     *
     * @return TombolaGain
     */
    public function setGainLib($gainLib)
    {
        $this->gainLib = $gainLib;

        return $this;
    }

    /**
     * Get gainLib
     *
     * @return string
     */
    public function getGainLib()
    {
        return $this->gainLib;
    }

    /**
     * Set gainValue
     *
     * @param integer $gainValue
     *
     * @return TombolaGain
     */
    public function setGainValue($gainValue)
    {
        $this->gainValue = $gainValue;

        return $this;
    }

    /**
     * Get gainValue
     *
     * @return integer
     */
    public function getGainValue()
    {
        return $this->gainValue;
    }
    
    /**
     * Get tombolaTirageGagnant
     *
     * @return TombolaTirageGagnant
    */
    function getTombolaTirageGagnant()
    {
        return $this->tombolaTirageGagnant;
    }
    
    /**
     * Set TombolaTirageGagnant
     *
     * @param TombolaSource $tombolaTirageGagnant
     *
     * @return TombolaTirageGagnant
     */
    function setTombolaTirageGagnant($tombolaTirageGagnant)
    {
        $this->tombolaTirageGagnant = $tombolaTirageGagnant;
    }
}

