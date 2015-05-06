<?php

namespace FIANET\SceauBundle\Controller\Emails;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QuestionnairesController extends Controller
{
    /**
     * Rend le template d'un e-mail.
     *
     * @param string $nomTemplate Nom du template (présent dans le paramétrage du type de questionnaire)
     *
     * @return \Symfony\Component\HttpFoundation\Response Instance de Reponse
     */
    public function rendreEmailQuestionnaireAction($nomTemplate)
    {
        return $this->render('FIANETSceauBundle:Emails/Questionnaires:'. $nomTemplate);
    }
}
