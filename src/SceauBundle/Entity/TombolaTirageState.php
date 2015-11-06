<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TombolaTirageState
 *
 * @ORM\Table(name="TombolaTirageState")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TombolaTirageStateRepository")
 */
class TombolaTirageState
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
     * @ORM\Column(name="stateLib", type="string", length=150)
     */
    private $stateLib;

    
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\TombolaTirage", mappedBy="tombolatiragestate")
    */
    private $tombolaTirage;


    /**
     * Set stateLib
     *
     * @param string $stateLib
     *
     * @return TombolaTirageState
     */
    public function setStateLib($stateLib)
    {
        $this->stateLib = $stateLib;

        return $this;
    }

    /**
     * Get stateLib
     *
     * @return string
     */
    public function getStateLib()
    {
        return $this->stateLib;
    }
    
    /**
     * Get tombolaTirage
     *
     * @return TombolaTirage
     */
    function getTombolaTirage()
    {
        return $this->tombolaTirage;
    }
    
    /**
     * Set TombolaTirage
     *
     * @param TombolaSource $tombolaTirage
     *
     * @return TombolaTirage
     */
    function setTombolaTirage($tombolaTirage)
    {
        $this->tombolaTirage = $tombolaTirage;
    }


}

