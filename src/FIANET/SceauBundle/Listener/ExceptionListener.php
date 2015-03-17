<?php

namespace FIANET\SceauBundle\Listener;

use FIANET\SceauBundle\Exception\Extranet\AccesInterditException;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    private $templating;

    public function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        /* Extranet : accès page non autorisée */
        if ($exception instanceof AccesInterditException) {
            $event->setResponse(
                $this->templating->renderResponse(
                    'FIANETSceauBundle:Extranet:acces_refuse.html.twig',
                    array(
                        'elementMenuTitre' => $exception->getTitre(),
                        'elementMenuDescriptif' => $exception->getMessage()
                    )
                )
            );
        }
    }
}
