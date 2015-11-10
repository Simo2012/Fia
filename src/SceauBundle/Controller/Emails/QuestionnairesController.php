<?php

namespace SceauBundle\Controller\Emails;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/* TODO : à revoir */
class QuestionnairesController extends Controller
{
    /**
     * Rend le template d'un e-mail.
     *
     * @param string $nomTemplate Nom du template (présent dans le paramétrage du type de questionnaire)
     * @param array $variables Tableau contenant les valeurs des variables contenues dans le template
     * @param bool $html Pour afficher ou non les balises de base d'un document HTML (html, body,...).
     *                   Vaut true par défaut.
     *
     * @return \Symfony\Component\HttpFoundation\Response Instance de Response
     */
    private function rendreEmailQuestionnaire($nomTemplate, $variables, $html = true)
    {
        return $this->render(
            'SceauBundle:Emails/Questionnaires:' . $nomTemplate . '.html.twig',
            ['html' => $html, 'variables' => $variables, 'locale' => $variables['locale'], 'tombola' => false,
                'livraisonPrevue' => false]
        );
    }

    /**
     * Rend le template d'un e-mail "normal".
     *
     * @param string $nomTemplate Nom du template (présent dans le paramétrage du type de questionnaire)
     * @param array $variables Tableau contenant les valeurs des variables contenues dans le template
     * @param bool $html Pour afficher ou non les balises de base d'un document HTML (html, body,...).
     *                   Vaut true par défaut.
     *
     * @return \Symfony\Component\HttpFoundation\Response Instance de Response
     */
    public function rendreEmailQuestionnaireNormalAction($nomTemplate, $variables, $html = true)
    {
        return $this->rendreEmailQuestionnaire($nomTemplate, $variables, $html);
    }

    /**
     * Rend le template d'un e-mail de relance.
     *
     * @param string $nomTemplate Nom du template (présent dans le paramétrage du type de questionnaire)
     * @param array $variables Tableau contenant les valeurs des variables contenues dans le template
     * @param bool $html Pour afficher ou non les balises de base d'un document HTML (html, body,...).
     *                   Vaut true par défaut.
     *
     * @return \Symfony\Component\HttpFoundation\Response Instance de Response
     */
    public function rendreEmailQuestionnaireRelanceAction($nomTemplate, $variables, $html = true)
    {
        return $this->rendreEmailQuestionnaire($nomTemplate. '_relance', $variables, $html);
    }
}
