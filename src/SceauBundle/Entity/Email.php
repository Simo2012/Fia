<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 *
 * @ORM\Table(name="Email")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\EmailRepository")
 */
class Email
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
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="principal", type="boolean")
     */
    private $principal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateConfirmation", type="datetimetz", nullable=true)
     */
    private $dateConfirmation;


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
     * Set email
     *
     * @param string $email
     * @return Email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set principal
     *
     * @param boolean $principal
     *
     * @return Email
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * Get principal
     *
     * @return boolean
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * Set dateConfirmation
     *
     * @param \DateTime $dateConfirmation
     *
     * @return Email
     */
    public function setDateConfirmation($dateConfirmation)
    {
        $this->dateConfirmation = $dateConfirmation;

        return $this;
    }

    /**
     * Get dateConfirmation
     *
     * @return \DateTime
     */
    public function getDateConfirmation()
    {
        return $this->dateConfirmation;
    }
}
