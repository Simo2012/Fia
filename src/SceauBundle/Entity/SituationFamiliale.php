<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SituationFamiliale
 *
 * @ORM\Table(name="SituationFamiliale")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\SituationFamilialeRepository")
 */
class SituationFamiliale
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
     * @ORM\Column(name="situation", type="string", length=200)
     */
    private $situation;


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
     * Set situation
     *
     * @param string $situation
     *
     * @return SituationFamiliale
     */
    public function setSituation($situation)
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * Get situation
     *
     * @return string
     */
    public function getSituation()
    {
        return $this->situation;
    }
}

