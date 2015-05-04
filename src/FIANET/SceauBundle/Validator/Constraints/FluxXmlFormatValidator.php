<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use DOMDocument;
use Exception;
use FIANET\SceauBundle\Entity\Flux;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FluxXmlFormatValidator extends ConstraintValidator
{
    private $router;
    private $templating;

    public function __construct(Router $router, TwigEngine $templating)
    {
        $this->router = $router;
        $this->templating = $templating;
    }

    /**
     * Méthode qui récupère le premier message d'erreur d'une invalidation d'un flux XML.
     * Ce message est formaté puis retourné.
     *
     * @param Constraint $constraint Instance de FluxXmlFormat
     *
     * @return string Le premier message d'erreur
     */
    private function getPremierMessageErreur(Constraint $constraint)
    {
        $erreurs = libxml_get_errors();

        if (empty($erreurs)) {
            $message = $constraint->message;
        } else {
            $message = '<![CDATA[Error ' . $erreurs[0]->code . ' - line ' .
                $erreurs[0]->line . ' - ' . $erreurs[0]->message . ']]>';
        }

        return $message;
    }

    /**
     * Vérifie le format du XML d'un flux grâce au schéma XML.
     * Si le flux n'est pas valide, la première erreur rencontrée est renvoyée.
     *
     * @param Flux $flux Instance de Flux
     *
     * @param Constraint $constraint Instance de FluxXmlFormat
     */
    public function validate($flux, Constraint $constraint)
    {
        libxml_use_internal_errors(true); // Désactivation de l'affichage des messages d'erreur sur la sortie standard

        try {
            $schema = $this->templating->render('FIANETSceauBundle:Webservice:send_rating_schema.xsd.twig');

            $doc = new DOMDocument();
            
            if ($doc->loadXML($flux->getXml())) {
                if (!$doc->schemaValidateSource($schema)) {
                    $this->buildViolation($this->getPremierMessageErreur($constraint))->addViolation();
                }
            } else {
                $this->buildViolation($constraint->message)->addViolation();
            }

        } catch (Exception $e) {
            $this->buildViolation('constraints.erreur_interne')->addViolation();
        }
    }
}
