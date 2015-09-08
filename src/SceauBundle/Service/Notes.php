<?php

namespace SceauBundle\Service;

use SceauBundle\Entity\QuestionnaireType;

class Notes
{
    /**
     * Méthode qui permet de récupérer pour un type de questionnaire les identifiants des réponses ou les bornes de
     * notation liés à un/des indicateur(s).
     *
     * @param QuestionnaireType $questionnaireType Instance de QuestionnaireType
     * @param array $indicateurs Tableau contenant les indicateurs souhaités (vert, jaune, rouge ou gris)
     *
     * @return array Tableau contenant 2 index : 'reponses' => les identifiants des réponses ou bornes de notations
     *     liées aux indicateurs choisis, 'nullable' => booléen indiquant si l'indicateur gris a été choisi
     */
    public function listeReponsesIndicateursPourQuestionnaireType(QuestionnaireType $questionnaireType, $indicateurs)
    {
        $listeReponses['reponses'] = array();
        $listeReponses['nullable'] = false;

        if ($questionnaireType->getParametrage()['indicateur']['type'] == 'reponse_id') {
            if (in_array('vert', $indicateurs)) {
                $listeReponses['reponses'] = array_merge(
                    $listeReponses['reponses'],
                    $questionnaireType->getParametrage()['indicateur']['vert']
                );
            }

            if (in_array('jaune', $indicateurs)) {
                $listeReponses['reponses'] = array_merge(
                    $listeReponses['reponses'],
                    $questionnaireType->getParametrage()['indicateur']['jaune']
                );
            }

            if (in_array('rouge', $indicateurs)) {
                $listeReponses['reponses'] = array_merge(
                    $listeReponses['reponses'],
                    $questionnaireType->getParametrage()['indicateur']['rouge']
                );
            }

        } else {
            if (in_array('vert', $indicateurs)) {
                $listeReponses['reponses'][] = array(
                    'min' => $questionnaireType->getParametrage()['indicateur']['vert']['min'],
                    'max' => $questionnaireType->getParametrage()['indicateur']['vert']['max']
                );
            }

            if (in_array('jaune', $indicateurs)) {
                $listeReponses['reponses'][] = array(
                    'min' => $questionnaireType->getParametrage()['indicateur']['jaune']['min'],
                    'max' => $questionnaireType->getParametrage()['indicateur']['jaune']['max']
                );
            }

            if (in_array('rouge', $indicateurs)) {
                $listeReponses['reponses'][] = array(
                    'min' => $questionnaireType->getParametrage()['indicateur']['rouge']['min'],
                    'max' => $questionnaireType->getParametrage()['indicateur']['rouge']['max']
                );
            }
        }

        if (in_array('gris', $indicateurs)) {
            $listeReponses['nullable'] = true ;
        }

        return $listeReponses;
    }
}
