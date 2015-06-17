<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class QuestionGlobale extends Constraint
{
    public $message = 'constraints.question_globale';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return 'validator.question_globale';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
