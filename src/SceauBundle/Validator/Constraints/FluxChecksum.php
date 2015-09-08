<?php

namespace SceauBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class FluxChecksum extends Constraint
{
    public $message = 'constraints.flux_checksum_incorrect';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
