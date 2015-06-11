<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class QuestionPerso extends Constraint
{
    public $message = 'constraints.question_perso';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return 'validator.question_perso';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
