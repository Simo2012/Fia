<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TombolaSource
 *
 * @ORM\Table(name="TombolaSource")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\TombolaSourceRepository")
 */
class TombolaSource
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
     * @ORM\Column(name="SourceLib", type="string", length=100)
     */
    private $sourceLib;

    /**
     * @var integer
     *
     * @ORM\Column(name="isLimited", type="integer")
     */
    private $isLimited;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SceauBundle\Entity\TombolaTicket", mappedBy="tombolasource")
    */
    private $tombolaTicket;
    
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
     * Set sourceLib
     *
     * @param string $sourceLib
     *
     * @return TombolaSource
     */
    public function setSourceLib($sourceLib)
    {
        $this->sourceLib = $sourceLib;

        return $this;
    }

    /**
     * Get sourceLib
     *
     * @return string
     */
    public function getSourceLib()
    {
        return $this->sourceLib;
    }

    /**
     * Set isLimited
     *
     * @param integer $isLimited
     *
     * @return TombolaSource
     */
    public function setIsLimited($isLimited)
    {
        $this->isLimited = $isLimited;

        return $this;
    }

    /**
     * Get isLimited
     *
     * @return integer
     */
    public function getIsLimited()
    {
        return $this->isLimited;
    }
    
    /**
     * Get tombolaTicket
     *
     * @return TombolaTicket
    */
    function getTombolaTicket()
    {
        return $this->tombolaTicket;
    }
    
    /**
     * Set TombolaTicket
     *
     * @param TombolaSource $tombolaTicket
     *
     * @return TombolaTicket
    */
    function setTombolaTicket($tombolaTicket)
    {
        $this->tombolaTicket = $tombolaTicket;
    }


}

