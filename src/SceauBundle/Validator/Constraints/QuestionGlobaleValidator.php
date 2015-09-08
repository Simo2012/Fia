<?php

namespace SceauBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\Question;
use SceauBundle\Entity\QuestionType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class QuestionGlobaleValidator extends ConstraintValidator
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Vérifie qu'une question est bien valide :
     * 1) Pour les questions de type notation, il faut que les bornes de notation soit bien définies.
     *
     * @param Question $question Instance de Question
     * @param Constraint $constraint Instance de QuestionGlobale
     */
    public function validate($question, Constraint $constraint)
    {
        if ($question->getQuestionType()->getId() == QuestionType::NOTATION &&
            ($question->getValeurMin() == '' || $question->getValeurMax() == '')) {
            $this->context->addViolation('constraints.question.notation');
        }
    }
}
