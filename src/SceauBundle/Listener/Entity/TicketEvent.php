<?php
namespace SceauBundle\Listener\Entity;

use Symfony\Component\EventDispatcher\Event;

class TicketEvent extends Event
{
    /** @var string */
    protected $test = null;

    public function setTest($test)
    {
        $this->test = $test;
    }

    public function getTest()
    {
        return $this->test;
    }
}