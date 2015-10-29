<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActiviteProfessionnelle
 *
 * @ORM\Table(name="ActiviteProfessionnelle")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\ActiviteProfessionnelleRepository")
 */
class ActiviteProfessionnelle
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
     * @ORM\Column(name="activite", type="string", length=200)
     */
    private $activite;


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
     * Set activite
     *
     * @param string $activite
     *
     * @return ActiviteProfessionnelle
     */
    public function setActivite($activite)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return string
     */
    public function getActivite()
    {
        return $this->activite;
    }
}

