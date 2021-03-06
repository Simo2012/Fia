<?php

namespace SceauBundle\Entity;

use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="envoiemail")
 * @ORM\Entity(repositoryClass="SceauBundle\Entity\Repository\EnvoiEmailRepository")
 */
class EnvoiEmail
{
    /**
     * CONSTANTS
     */
    const NOT_SENT = 0;
    const SUCCESS = 1;
    const ERROR = 2;

    protected static $STATUS_EMAIL = [
        self::NOT_SENT => 'Non envoyé',
        self::SUCCESS => 'Succès',
        self::ERROR => 'Erreur'
    ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sendFrom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sendTo;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $dateInsert;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $dateSend;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;


    public function __construct()
    {
        $this->dateInsert = new \DateTime();
    }


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
     * Set subject
     *
     * @param string $subject
     *
     * @return EnvoiEmail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set sendFrom
     *
     * @param string $sendFrom
     *
     * @return EnvoiEmail
     */
    public function setSendFrom($sendFrom)
    {
        $this->sendFrom = $sendFrom;

        return $this;
    }

    /**
     * Get sendFrom
     *
     * @return string
     */
    public function getSendFrom()
    {
        return $this->sendFrom;
    }

    /**
     * Set sendTo
     *
     * @param string $sendTo
     *
     * @return EnvoiEmail
     */
    public function setSendTo($sendTo)
    {
        $this->sendTo = $sendTo;

        return $this;
    }

    /**
     * Get sendTo
     *
     * @return string
     */
    public function getSendTo()
    {
        return $this->sendTo;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return EnvoiEmail
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateInsert
     *
     * @param \DateTime $dateInsert
     *
     * @return EnvoiEmail
     */
    public function setDateInsert($dateInsert)
    {
        $this->dateInsert = $dateInsert;

        return $this;
    }

    /**
     * Get dateInsert
     *
     * @return \DateTime
     */
    public function getDateInsert()
    {
        return $this->dateInsert;
    }

    /**
     * Set dateSend
     *
     * @param \DateTime $dateSend
     *
     * @return EnvoiEmail
     */
    public function setDateSend($dateSend)
    {
        $this->dateSend = $dateSend;

        return $this;
    }

    /**
     * Get dateSend
     *
     * @return \DateTime
     */
    public function getDateSend()
    {
        return $this->dateSend;
    }

    /**
     * Set status
     *
     * @param int $int Identifiant du statut
     *
     * @return EnvoiEmail
     */
    public function setStatus($int)
    {
        if (array_key_exists($int, self::$STATUS_EMAIL)) {
            $this->status = $int;
            return $this;

        } else {
            throw new Exception("Not status for this int");
        }
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return self::$STATUS_EMAIL[$this->status];
    }
}
