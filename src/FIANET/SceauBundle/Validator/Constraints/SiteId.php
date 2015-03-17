<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class SiteId extends Constraint
{
    public $message = 'constraints.site_id_incorrect';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return 'validator.site_id';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
