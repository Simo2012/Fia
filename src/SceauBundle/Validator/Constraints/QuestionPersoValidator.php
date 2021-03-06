<?php

namespace SceauBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectManager;
use SceauBundle\Entity\Question;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class QuestionPersoValidator extends ConstraintValidator
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Vérifie que la création d'une question personnalisée respecte les différentes régles de gestion.
     * Si on a bien la date de début et la date de fin, on teste si :
     * 1) Date de début < Date de fin
     * 2) Date de début doit être au minimum égale à J+1
     * 3) Pas plus de 2 questions sur une même période.
     * 4) Pour les choix multiples, seulement une demande de précision au maximum.
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
        if ($question->getDateDebut() && $question->getDateFin()) {
            if ($question->getDateDebut() > $question->getDateFin()) {
                $this->context->addViolation('constraints.question_perso.date_debut_gt_date_fin');
                return false;

            } else {
                $demain = new \DateTime('tomorrow');
                if ($question->getDateDebut() < $demain) {
                    $this->context->addViolation('constraints.question_perso.date_debut_demain');
                    return false;

                } else {
                    $nb = $this->em->getRepository('SceauBundle:Question')->nbQuestionPersoPourUnePeriode(
                        $question->getSite(),
                        $question->getQuestionnaireTypes()[0],
                        $question->getDateDebut(),
                        $question->getDateFin()
                    );
                    if ($nb >= 2) {
                        $this->context->addViolation('constraints.question_perso.quotas');
                        return false;
                    }
                }
            }
        }

        $nbPrecision = 0;
        foreach ($question->getReponses() as $reponse) {
            if ($reponse->getPrecision()) {
                $nbPrecision++;
            }
        }
        if ($nbPrecision > 1) {
            $this->context->addViolation('constraints.question_perso.nb_precision');

            return false;
        }

        return true;
    }
}
