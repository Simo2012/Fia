<?php

namespace SceauBundle\Validator\Constraints;

use DOMDocument;
use Exception;
use SceauBundle\Entity\Flux;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FluxXmlFormatValidator extends ConstraintValidator
{
    private $router;
    private $templating;

    public function __construct(Router $router, EngineInterface $templating)
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
            $schema = $this->templating->render('SceauBundle:Webservice:send_rating_schema.xsd.twig');

            $doc = new DOMDocument();
            
            if ($doc->loadXML($flux->getXml())) {
                if (!$doc->schemaValidateSource($schema)) {
                    $this->context->addViolation($this->getPremierMessageErreur($constraint));
                }
            } else {
                $this->context->addViolation($constraint->message);
            }

        } catch (Exception $e) {
            $this->context->addViolation('constraints.erreur_interne');
        }
    }
}
