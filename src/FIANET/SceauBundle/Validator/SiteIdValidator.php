<?php

namespace FIANET\SceauBundle\Validator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SiteIdValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Vérifie que le site ID passé en argument est un numérique et qu'il existe un site en base avec cet identifiant.
     *
     * @param int $site_id Identifiant d'un site
     * @param Constraint $constraint Instance de SiteID
     */
    public function validate($site_id, Constraint $constraint)
    {
        $siteIdIncorrect = false;

        if (is_numeric($site_id)) {
            $site = $this->em->getRepository('FIANETSceauBundle:Site')->find($site_id);

            if (!$site) {
                $siteIdIncorrect = true;
            }
        } else {
            $siteIdIncorrect = true;
        }

        if ($siteIdIncorrect) {
            $this->buildViolation($constraint->message)->addViolation();
        }
    }
}
