<?php

namespace FIANET\SceauBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use FIANET\SceauBundle\Entity\Question;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class QuestionPersoValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Vérifie que la création d'une question personnalisée respecte les régles de gestion.
     * 1) Date de début < Date de fin
     * 2) Date de début doit être au minimum égale à J+1
     * 3) Pas plus de 2 questions sur une même période.
     *
     * TODO : il faut rajouter les contrôles sur les options et date de garantie
     *
     * @param Question $question Instance de Question
     * @param Constraint $constraint Instance de QuestionPerso
     *
     * @return bool true Si valide retourne true sinon false
     */
    public function validate($question, Constraint $constraint)
    {
        if ($question->getDateFin() && ($question->getDateDebut() > $question->getDateFin())) {
            $this->buildViolation('constraints.question_perso.date_debut_gt_date_fin')->addViolation();
            return false;
        }

        $demain = new \DateTime('tomorrow');
        if ($question->getDateDebut() < $demain) {
            $this->buildViolation('constraints.question_perso.date_debut_demain')->addViolation();
            return false;
        }

        $nb = $this->em->getRepository('FIANETSceauBundle:Question')->nbQuestionPersoPourUnePeriode(
            $question->getSite(),
            $question->getQuestionnaireType(),
            $question->getDateDebut(),
            $question->getDateFin()
        );
        if ($nb >= 2) {
            $this->buildViolation('constraints.question_perso.quotas')->addViolation();
            return false;
        }

        return true;
    }
}
