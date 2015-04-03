<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class FluxXmlContenu extends Constraint
{
    public $message = 'constraints.flux_xml_invalide';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return 'validator.flux_xml_contenu';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
