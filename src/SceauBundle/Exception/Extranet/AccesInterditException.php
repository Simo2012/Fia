<?php

namespace SceauBundle\Exception\Extranet;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AccesInterditException extends HttpException
{
    private $titre;

    /**
     * Constructeur
     *
     * @param string $titre Titre à afficher dans la page
     * @param string $message Message à afficher dans la page
     */
    public function __construct($titre, $message)
    {
        parent::__construct(403, $message, null, array(), 0);
    }

    public function getTitre()
    {
        return $this->titre;
    }
}
