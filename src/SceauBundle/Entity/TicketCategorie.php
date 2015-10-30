<?php

namespace SceauBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketCategorie
 *
 * @ORM\Embeddable
 */
class TicketCategorie
{
    // type
    const TYPE_AVIS      = 1;
    const TYPE_CONTACT   = 2;

    public static $TYPES = [
        self::TYPE_AVIS      => 'Avis',
        self::TYPE_CONTACT   => 'Contact',
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

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
     * Set id
     *
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return isset(self::$TYPES[$this->id]) ? self::$TYPES[$this->id] : '';
    }

    public static function getAvailableTypes()
    {
        return self::$TYPES;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
    }
}
