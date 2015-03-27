<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use FIANET\SceauBundle\Entity\Flux;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FluxChecksumValidator extends ConstraintValidator
{
    /**
     * Vérifie que pour un flux : checksum = md5(xml).
     *
     * @param Flux $flux Instance de Flux
     *
     * @param Constraint $constraint Instance de FluxChecksum
     */
    public function validate($flux, Constraint $constraint)
    {
        if ($flux->getChecksum() != md5($flux->getXml())) {
            $this->buildViolation($constraint->message)->addViolation();
        }
    }
}
