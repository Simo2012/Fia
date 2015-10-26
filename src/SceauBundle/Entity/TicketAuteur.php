<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="TicketAuteur")
 * @ORM\Entity
 */
class TicketAuteur
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
     * @var Member
     *
     * @ORM\OneToOne(targetEntity="SceauBundle\Entity\Ticket", inversedBy="auteur", cascade={"persist"})
     */
    private $ticket;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="text")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="text")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="text")
     */
    private $phone;

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
     * Set lastName
     *
     * @param string $lastName
     *
     * @return TicketAuteur
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return TicketAuteur
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return TicketAuteur
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
     * Set phone
     *
     * @param string $phone
     *
     * @return TicketAuteur
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set ticket
     *
     * @param \SceauBundle\Entity\Ticket $ticket
     *
     * @return TicketAuteur
     */
    public function setTicket(\SceauBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \SceauBundle\Entity\Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}
