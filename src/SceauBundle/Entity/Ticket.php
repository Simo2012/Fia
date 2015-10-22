<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table(name="Ticket")
 * @ORM\Entity
 */
class Ticket
{
    // ACTOR
    const INDIVIDUAL = 1;
    const PROFESSIONAL = 2;

    public static $ACTORS = [
        self::INDIVIDUAL   => 'Un particulier',
        self::PROFESSIONAL => 'Un professionnel',
    ];

    // TYPE INDIVIDUAL
    const COMMAND_CANCEL                = 11;
    const COMMAND_AWAIT                 = 12;
    const WEBSITE_TECHNICAL_PROBLEM     = 13;
    const MEMBER_AREA_TECHNICAL_PROBLEM = 14;
    const LITIGATION                    = 15;
    const JOB_OFFER                     = 16;
    const PHISHING                      = 17;

    // TYPE PROFESSIONAL
    const REPORTER   = 21;
    const E_MERCHANT = 22;
    const WEB_AGENCY = 23;

    public static $TYPES = [
        self::INDIVIDUAL => [
            self::COMMAND_CANCEL                => 'Une commande annulée par un e-commerçant',
            self::COMMAND_AWAIT                 => 'Une commande en attente de pièces justificatives',
            self::WEBSITE_TECHNICAL_PROBLEM     => 'Un problème technique sur le site FIA-NET.com',
            self::MEMBER_AREA_TECHNICAL_PROBLEM => 'Un problème technique dans votre espace membre FIA-NET',
            self::LITIGATION                    => 'La déclaration d\'un litige avec un e-commerçant sur FIA-NET.com',
            self::JOB_OFFER                     => 'Les offres d\'emplois disponibles',
            self::PHISHING                      => 'Une tentative de phishing concernant FIA-NET',
        ],
        self::PROFESSIONAL => [
            self::REPORTER   => 'Un journaliste',
            self::E_MERCHANT => 'Un e-commerçant souhaitant être informé sur nos offres',
            self::WEB_AGENCY => 'Une web agency souhaitant devenir partenaire du Sceau de Confiance FIA-NET',
        ],
    ];

    public static $TYPES_TEMPLATE = [
        self::COMMAND_CANCEL => 'SceauBundle:Site\Contact:command.html.twig',
        self::COMMAND_AWAIT  => 'SceauBundle:Site\Contact:command.html.twig',
        self::LITIGATION     => 'SceauBundle:Site\Contact:litigation.html.twig',
        self::JOB_OFFER      => 'SceauBundle:Site\Contact:job_offer.html.twig',
        self::PHISHING       => 'SceauBundle:Site\Contact:phishing.html.twig',
        self::REPORTER       => 'SceauBundle:Site\Contact:reporter.html.twig',
        self::E_MERCHANT     => 'SceauBundle:Site\Contact:e_merchant.html.twig',
        self::WEB_AGENCY     => 'SceauBundle:Site\Contact:web_agency.html.twig',
    ];

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
     * @ORM\Column(name="question", type="text")
     */
    private $question;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat;

    private $actor;

    private $type;

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
     * Set question
     *
     * @param string $question
     *
     * @return Ticket
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Ticket
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set etat
     *
     * @param boolean $etat
     *
     * @return Ticket
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean
     */
    public function getEtat()
    {
        return $this->etat;
    }


    public function getActor()
    {
        return $this->actor;
    }
    public function getActorLabel()
    {
        return self::$ACTORS[$this->actor];
    }
    public function setActor($actor)
    {
        if (array_key_exists($actor, self::$ACTORS)) {
            $this->actor = $actor;
        }

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }
    public function getTypeLabel()
    {
        return self::$TYPES[$this->type];
    }
    public function setType($type)
    {
        if ($this->actor) {
            if (array_key_exists($type, self::$TYPES[$this->actor])) {
                $this->type = $type;
            }
        }

        return $this;
    }
    public static function getAvailableTypes($actor)
    {
        if ($actor) {
            return self::$TYPES[$actor];
        }

        return [];
    }
}
