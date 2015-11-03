<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avatar
 *
 * @ORM\Table(name="Avatar")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\AvatarRepository")
 */
class Avatar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="smallint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="smallint")
     */
    private $number;


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
     * Get number
     *
     * @return Avatar
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set Number
     *
     * @param integer $number
     *
     * @return Avatar
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }
}
